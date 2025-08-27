// courses.js — fetch real CPT data, mix with demo fields
(function () {
  // init every block on the page
  document.querySelectorAll(".bc-wrap").forEach(function (root) {
    if (!root) return;

    // ---- Elements (scoped) ----
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

    let courses = []; // will fill via API
    const state = { page: 1, perPage: 8, sort: "relevance", pages: 1 };

    // --- Helpers ---
    const $$ = (sel, parent = sidebar) =>
      Array.from(parent.querySelectorAll(sel));

    function ratingStarsHTML(r, provider, reviews, html) {
      return `
    <div class="bc-rating-container">
      <div class="col">
        <span class="bc-rating">${r.toFixed(1)}</span>
        <span class="bc-stars" aria-label="${r.toFixed(1)} out of 5">
          <span class="bc-starsbox">${html}</span>
        </span>
        <span class="bc-chip">${reviews} reviews</span>
      </div>
      ${
        provider
          ? `<div class="col"><span class="bc-provider">${provider}</span></div>`
          : ""
      }
    </div>`;
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
      const decodeHTML = (str) => {
        const txt = document.createElement("textarea");
        txt.innerHTML = str;
        return txt.value;
      };

      const f = {};
      ["rating", "category", "outcomes", "level", "price", "duration"].forEach(
        (g) => {
          f[g] = $$(`input[name="${g}"]:checked`).map((i) =>
            decodeHTML(i.value.trim())
          );
        }
      );
      return f;
    }
    function applyFilters(arr) {
      const f = getFilters();
      return arr.filter((c) => {
        if (f.rating.length && !f.rating.some((v) => c.rating >= parseFloat(v)))
          return false;
        if (
          f.category.length &&
          !f.category.some((cat) => c.categories.includes(cat))
        )
          return false;
        if (
          f.outcomes.length &&
          !f.outcomes.some((o) => c.outcomeLabels.includes(o))
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
        case "rating_desc": // highest rated
          return cp.sort(by("rating", -1));
        case "newest": // most recently added
          return cp.sort(by("id", -1)); // higher ID = newer (WP style)
        case "relevance": // just return as-is
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
      }" ${prevDis}><ion-icon name="arrow-back-outline"></ion-icon> Previous</button>`;
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
      }" ${nextDis}>Next <ion-icon name="arrow-forward-outline"></ion-icon></button>`;
      pagEl.innerHTML = html;
    }

    function outcomesHTML(outcomes) {
      if (!outcomes || Object.keys(outcomes).length === 0) {
        return '<span class="bc-kpi bc-muted">No outcomes data yet</span>';
      }

      const themeDirectory = "/wp-content/themes/reviewmvp";
      const iconPath = `${themeDirectory}/assets/media/`;

      return Object.entries(outcomes)
        .map(([label, icon]) => {
          return `<span class="bc-kpi">
        <img src="${iconPath}${icon}" alt="${label}"> ${label}
      </span>`;
        })
        .join("");
    }

    // --- Card ---
    function cardHTML(c) {
      const yesPct = c.worthYes,
        recPct = c.recommend;

      // worth percentage color
      let worthColor = "#DC2625";
      if (yesPct >= 70) {
        worthColor = "#11B981";
      } else if (yesPct >= 30) {
        worthColor = "#F6C701";
      }

      // recommend ribbon colors
      let recColor = "#DC2625";
      let recBg = "#FEF1F2";
      if (recPct >= 70) {
        recColor = "#11B981";
        recBg = "#e0f6ef";
      } else if (recPct >= 30) {
        recColor = "#F6C701";
        recBg = "#fbf9e2";
      }

      const themeDirectory = "/wp-content/themes/reviewmvp";
      const iconPath = `${themeDirectory}/assets/media/`;

      // --- Metas (duration + level) ---
      let metasHTML = "";
      if (c.durationHours) {
        metasHTML += `<span class="bc-meta"><img src="${iconPath}icon-duration.svg" alt="Duration Icon"> ${formatDuration(
          c.durationHours
        )}</span>`;
      }
      if (c.level) {
        metasHTML += `<span class="bc-meta"><img src="${iconPath}icon-level.svg" alt="Level Icon"> ${c.level}</span>`;
      }
      const metasBlock = metasHTML
        ? `<div class="bc-metas">${metasHTML}</div>`
        : "";

      // --- Outcomes ---
      let outcomesBlock = "";
      if (c.outcomes && Object.keys(c.outcomes).length > 0) {
        outcomesBlock = `<div class="bc-kpis">
        <span class="bc-kpi-label">
          <img src="${iconPath}icon-outcome.svg" alt="Outcome Icon"> Students Outcome:
        </span>
        ${outcomesHTML(c.outcomes)}
      </div>`;
      }

      return `<article class="bc-card" data-id="${c.id}">
      <div class="bc-starsline">${ratingStarsHTML(
        c.rating,
        c.provider,
        c.reviews,
        c.ratingHTML
      )}</div>
      <div class="bc-info">
          <a href="${c.link}" class="bc-title">${c.title}</a>
          ${
            c.instructor
              ? `<div class="bc-author" style="font-size:13px">By ${c.instructor}</div>`
              : ""
          }
          <div class="bc-description">${c.description}</div>
      </div>
      ${metasBlock}
      ${outcomesBlock}
      <div class="bc-bottom-container">
          <div class="bc-worth">
              <img src="${iconPath}icon-worth.svg" alt="Worth Icon">
              Worth the money? <strong style="color:${worthColor}">${yesPct}% say YES</strong>
          </div>
          <span class="bc-ribbon" style="color:${recColor};background-color:${recBg}">
            ${recPct}% of students recommend this course
          </span>
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
    // Auto-resize sort <select> width to fit selected option
    (function (select) {
      function resize() {
        const temp = document.createElement("span");
        temp.style.visibility = "hidden";
        temp.style.position = "absolute";
        temp.style.whiteSpace = "nowrap";
        temp.textContent = select.options[select.selectedIndex].text;
        document.body.appendChild(temp);

        // Add ~40px padding for the dropdown arrow
        select.style.width = temp.offsetWidth + 60 + "px";

        temp.remove();
      }

      resize(); // run once at start
      select.addEventListener("change", resize);
    })(sortEl);
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

    // Toggle on click
    root.querySelectorAll(".bc-fhead").forEach((h) => {
      h.addEventListener("click", () => {
        const body = h.parentElement.querySelector(".bc-fbody");
        const icon = h.querySelector("ion-icon");

        body.classList.toggle("open");
        if (icon) {
          icon.classList.toggle("open", body.classList.contains("open"));
        }
      });
    });

    // --- Mobile sidebar toggle ---
    const backdrop = root.querySelector(".bc-backdrop");
    const filterToggle = root.querySelector(".bc-filter-toggle");

    if (filterToggle && backdrop) {
      filterToggle.addEventListener("click", () => {
        sidebar.classList.add("open");
        backdrop.classList.add("active");
      });

      backdrop.addEventListener("click", () => {
        sidebar.classList.remove("open");
        backdrop.classList.remove("active");
      });
    }

    // map stored level slug to display label
    const levelLabels = {
      beginner: "Beginner",
      intermediate: "Intermediate",
      advance: "Advance",
    };

    // --- Fetch real data from REST API ---
    async function fetchCourses() {
      try {
        const res = await fetch("/wp-json/wp/v2/course?per_page=50");
        const data = await res.json();
        // map stored level slug to display label
        const levelLabels = {
          beginner: "Beginner",
          intermediate: "Intermediate",
          advance: "Advance",
        };
        let allCategories = new Set();
        courses = data.map((c) => {
          const rawLevel = c.course_level || "";
          const level = levelLabels[rawLevel] || rawLevel;
          const rawPrice = parseFloat(c.course_price) || 0;
          const isFree = rawPrice <= 0;

          // outcomes
          const outcomesObj = c.outcomes_data || {};
          const outcomeLabels = Object.keys(outcomesObj).map((k) =>
            k.replace(/\s*\(\d+%?\)/, "").trim()
          );

          // categories
          const categories = (c.course_categories || []).map((cat) => cat.name);
          categories.forEach((cat) => allCategories.add(cat));

          return {
            id: c.id,
            date: c.date,
            title: c.title.rendered,
            description: c.excerpt?.rendered || "",
            provider: c.course_provider || "",
            instructor: c.course_instructor?.name || "",
            durationHours: parseFloat(c.course_duration) || 0,
            level: level,
            link: c.link,
            rating: parseFloat(c.rating_data?.average) || 0,
            reviews: parseInt(c.rating_data?.count) || 0,
            ratingHTML: c.rating_html || "",
            outcomes: outcomesObj,
            outcomeLabels: outcomeLabels,
            worthYes: parseFloat(c.review_stats?.worth) || 0,
            recommend: parseFloat(c.review_stats?.recommend) || 0,
            price: rawPrice,
            isFree: isFree,
            categories: categories,
          };
        });
        // Inject categories dynamically
        const catContainer = root.querySelector(
          '[data-role="category-options"]'
        );
        if (catContainer) {
          catContainer.innerHTML = Array.from(allCategories)
            .sort()
            .map(
              (cat) =>
                `<label class="bc-check">
          <input type="checkbox" name="category" value="${cat}"> ${cat}
        </label>`
            )
            .join("");
        }
        render();
      } catch (err) {
        console.error("Error fetching courses:", err);
        resultsEl.innerHTML =
          '<div class="bc-muted">Unable to load courses.</div>';
      }
    }

    fetchCourses();
  });
})();
