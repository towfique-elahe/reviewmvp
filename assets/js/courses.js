// courses.js  — no PHP required
(function () {
  // init every block on the page
  document.querySelectorAll(".bc-wrap").forEach(function (root) {
    if (!root) return;

    // ---- Elements (scoped) ----
    // Select by id suffix so it works with whatever UID PHP prints
    const resultsEl =
      root.querySelector('[id$="_results"]') ||
      root.querySelector('[data-role="results"]');
    const sortEl =
      root.querySelector('[id$="_sort"]') ||
      root.querySelector('[data-role="sort"]');
    const pagEl =
      root.querySelector('[id$="_pagination"]') ||
      root.querySelector('[data-role="pagination"]');
    const countEl = root.querySelector(".bc-count");
    const sidebar = root.querySelector(".bc-sidebar");

    if (!resultsEl || !sortEl || !pagEl || !sidebar) return;

    // --- Demo data (24 items) ---
    let seed = 42;
    function rnd() {
      seed = (seed * 1664525 + 1013904223) % 4294967296;
      return seed / 4294967296;
    }
    const cats = [
      "UI/UX",
      "Dropshipping",
      "Software development",
      "Data analytics",
    ];
    const lvls = ["Beginner", "Intermediate", "Advanced"];
    const outsAll = [
      "Improved skills",
      "Built project",
      "Career boost",
      "Gained confidence",
      "Earned income",
      "No impact",
    ];
    const courses = Array.from({ length: 24 }, (_, i) => {
      const isFree = rnd() < 0.2;
      const price = isFree ? 0 : Math.round((rnd() * 49 + 5) * 100) / 100;
      const dur = Math.round((rnd() * 20 + 0.5) * 2) / 2;
      const outcomes = outsAll.filter(() => rnd() > 0.55);
      const rating = Math.round((3 + rnd() * 2) * 10) / 10;
      const rec = Math.round((0.3 + rnd() * 0.65) * 100) / 100;
      const worth = Math.round((0.1 + rnd() * 0.85) * 100) / 100;
      const tone = rec < 0.45 ? "red" : rec < 0.6 ? "amber" : "green";
      return {
        id: i + 1,
        title: "IoT Networks and Protocols",
        author: "Jose Portilla",
        provider: "Udemy",
        category: cats[i % cats.length],
        level: lvls[i % lvls.length],
        rating,
        reviews: 50 + Math.floor(rnd() * 400),
        durationHours: dur,
        price,
        isFree,
        outcomes: outcomes.length ? outcomes : ["Learned skill"],
        recommend: rec,
        worthYes: worth,
        ribbonTone: tone,
      };
    });

    const state = { page: 1, perPage: 8, sort: "relevance", pages: 1 };

    // --- Helpers ---
    const $$ = (sel, parent = sidebar) =>
      Array.from(parent.querySelectorAll(sel));
    function ratingStarsHTML(r) {
      return `<span class="bc-stars" aria-label="${r} out of 5">
        ${"★"
          .repeat(5)
          .split("")
          .map(() => `<span class="bc-star">★</span>`)
          .join("")}
      </span><span style="font-weight:700;margin-left:6px">${r.toFixed(
        1
      )}</span>
      <span class="bc-chip">${Math.max(
        ...courses.map((c) => c.reviews)
      )} reviews</span>`;
    }
    const formatDuration = (h) =>
      h < 1
        ? `${Math.round(h * 60)} mins`
        : `${h % 1 === 0 ? h : h.toFixed(1)} hours`;
    function inDurationBucket(h, b) {
      switch (b) {
        case "0-1":
          return h > 0 && h <= 1;
        case "1-3":
          return h > 1 && h <= 3;
        case "3-6":
          return h > 3 && h <= 6;
        case "6-17":
          return h > 6 && h <= 17;
        case "17+":
          return h > 17;
        default:
          return true;
      }
    }

    // --- Filters ---
    function getFilters() {
      const f = {};
      ["rating", "category", "outcomes", "level", "price", "duration"].forEach(
        (g) => {
          f[g] = $$(`input[name="${g}"]:checked`).map((i) => i.value);
        }
      );
      return f;
    }
    function applyFilters(arr) {
      const f = getFilters();
      return arr.filter((c) => {
        if (f.rating.length && !f.rating.some((v) => c.rating >= parseFloat(v)))
          return false;
        if (f.category.length && !f.category.includes(c.category)) return false;
        if (
          f.outcomes.length &&
          !f.outcomes.every((o) => c.outcomes.includes(o))
        )
          return false;
        if (f.level.length && !f.level.includes(c.level)) return false;
        if (f.price.length) {
          const wantsFree = f.price.includes("Free"),
            wantsPaid = f.price.includes("Paid");
          if (!((wantsFree && c.isFree) || (wantsPaid && !c.isFree)))
            return false;
        }
        if (
          f.duration.length &&
          !f.duration.some((b) => inDurationBucket(c.durationHours, b))
        )
          return false;
        return true;
      });
    }

    // --- Sorting ---
    function sortCourses(arr) {
      const v = state.sort,
        cp = [...arr];
      const by =
        (k, dir = 1) =>
        (a, b) =>
          a[k] > b[k] ? dir : a[k] < b[k] ? -dir : 0;
      switch (v) {
        case "rating_desc":
          return cp.sort(by("rating", -1));
        case "rating_asc":
          return cp.sort(by("rating", 1));
        case "duration_asc":
          return cp.sort(by("durationHours", 1));
        case "duration_desc":
          return cp.sort(by("durationHours", -1));
        case "price_asc":
          return cp.sort(by("price", 1));
        case "price_desc":
          return cp.sort(by("price", -1));
        default:
          return cp;
      }
    }

    // --- Pagination ---
    function paginate(arr) {
      const total = arr.length,
        pages = Math.max(1, Math.ceil(total / state.perPage));
      if (state.page > pages) state.page = pages;
      const start = (state.page - 1) * state.perPage;
      return { slice: arr.slice(start, start + state.perPage), total, pages };
    }
    function renderPagination(p) {
      if (p.pages <= 1) {
        pagEl.innerHTML = "";
        return;
      }
      const prevDis = state.page === 1 ? "disabled" : "";
      const nextDis = state.page === p.pages ? "disabled" : "";
      let start = Math.max(1, state.page - 3),
        end = Math.min(p.pages, start + 6);
      start = Math.max(1, end - 6);
      let html = `<button class="bc-pagebtn" data-page="${
        state.page - 1
      }" ${prevDis}>Prev</button>`;
      if (start > 1) {
        html += `<button class="bc-pagebtn" data-page="1">1</button><span class="bc-ellipsis">…</span>`;
      }
      for (let i = start; i <= end; i++) {
        html += `<button class="bc-pagebtn ${
          i === state.page ? "active" : ""
        }" data-page="${i}">${i}</button>`;
      }
      if (end < p.pages) {
        html += `<span class="bc-ellipsis">…</span><button class="bc-pagebtn" data-page="${p.pages}">${p.pages}</button>`;
      }
      html += `<button class="bc-pagebtn" data-page="${
        state.page + 1
      }" ${nextDis}>Next</button>`;
      pagEl.innerHTML = html;
    }

    // --- Card ---
    function cardHTML(c) {
      const yesPct = Math.round(c.worthYes * 100),
        recPct = Math.round(c.recommend * 100);
      const tone =
        c.ribbonTone === "red"
          ? "red"
          : c.ribbonTone === "amber"
          ? "amber"
          : "";
      return `<article class="bc-card" data-id="${c.id}">
        <div class="bc-row">
          <div>
            <div class="bc-starsline">${ratingStarsHTML(c.rating)}</div>
            <div class="bc-title">IoT Networks and Protocols</div>
            <div class="bc-muted" style="font-size:13px">By ${c.author}</div>
            <div class="bc-meta">
              <span>${formatDuration(c.durationHours)}</span>
              <span class="bc-dot"></span><span>${c.level}</span>
              <span class="bc-dot"></span>
              <span style="border:1px solid var(--bc-border);padding:3px 8px;border-radius:999px;background:#f8fafc;font-size:12px">${
                c.provider
              }</span>
            </div>
            <div class="bc-kpis">
              <span class="bc-kpi">Students Outcome:</span>
              <span class="bc-kpi">Learned skill (7%)</span>
              <span class="bc-kpi">Built project (8%)</span>
              <span class="bc-kpi">No impact (1%)</span>
            </div>
            <div class="bc-worth"><span class="bc-muted">Worth the money?</span> <strong>${yesPct}% say YES</strong></div>
            <span class="bc-ribbon ${tone}">${recPct}% of students recommend this course</span>
          </div>
          <span class="bc-badge">${
            c.isFree ? "Free" : "$" + c.price.toFixed(2)
          }</span>
        </div>
      </article>`;
    }

    // --- Render ---
    function render() {
      let arr = applyFilters(courses);
      arr = sortCourses(arr);
      const page = paginate(arr);
      state.pages = page.pages;
      resultsEl.innerHTML =
        page.slice.map(cardHTML).join("") ||
        '<div class="bc-muted">No courses match your filters.</div>';
      if (countEl)
        countEl.textContent = `${page.total} result${
          page.total === 1 ? "" : "s"
        }`;
      renderPagination(page);
    }

    // --- Events ---
    sortEl.addEventListener("change", function () {
      state.sort = this.value;
      state.page = 1;
      render();
    });
    sidebar.addEventListener("change", function (e) {
      if (e.target.matches('input[type="checkbox"]')) {
        state.page = 1;
        render();
      }
    });
    root.addEventListener("click", function (e) {
      const btn = e.target.closest(".bc-pagebtn");
      if (btn && btn.dataset.page) {
        const p = parseInt(btn.dataset.page, 10);
        if (!isNaN(p)) {
          state.page = Math.min(Math.max(1, p), state.pages);
          render();
        }
      }
      if (e.target.matches('[data-action="clear"]')) {
        Array.from(
          sidebar.querySelectorAll('input[type="checkbox"]:checked')
        ).forEach((i) => (i.checked = false));
        state.page = 1;
        render();
      }
    });
    // accordion
    root.querySelectorAll(".bc-fhead").forEach((h) => {
      h.addEventListener("click", () => {
        const b = h.parentElement.querySelector(".bc-fbody");
        b.style.display = b.style.display === "none" ? "" : "none";
      });
    });

    render();
  });
})();
