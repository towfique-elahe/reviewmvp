(function () {
  const wrap = document.querySelector("#searchCourse");
  if (!wrap) return;

  const input = wrap.querySelector("#courseSearchInput");
  const popover = wrap.querySelector(".results-popover");
  const list = wrap.querySelector("#courseSearchList");
  const addMissing = wrap.querySelector(".add-missing");
  const items =
    (window.courseSearchData && window.courseSearchData.items) || [];
  const addMissingUrl =
    window.courseSearchData?.addMissingUrl || "/add-missing-course/";
  const labels = window.courseSearchData?.labels || {
    course: "Course",
    category: "Category",
    noMatch: "Add missing course",
  };

  let activeIndex = -1; // keyboard focus index
  let currentResults = [];

  function open() {
    wrap.classList.add("is-open");
    wrap.setAttribute("aria-expanded", "true");
    popover.hidden = false;
  }

  function close() {
    wrap.classList.remove("is-open");
    wrap.setAttribute("aria-expanded", "false");
    popover.hidden = true;
    activeIndex = -1;
    input.setAttribute("aria-activedescendant", "");
  }

  function render(results) {
    currentResults = results;
    list.innerHTML = "";
    if (results.length) {
      addMissing.style.display = "none";
      results.forEach((r, i) => {
        const li = document.createElement("li");
        li.className = "result-item";
        li.id = `cs-opt-${i}`;
        li.setAttribute("role", "option");
        li.setAttribute("aria-selected", "false");
        li.innerHTML = `
          <div class="result-name">${r.name}</div>
          <div class="result-type">${labels[r.type] || ""}</div>
        `;
        li.addEventListener("mousedown", (e) => {
          e.preventDefault();
          select(i);
        });
        list.appendChild(li);
      });
    } else {
      addMissing.style.display = "flex";
    }
  }

  function filter(q) {
    const s = q.trim().toLowerCase();
    if (!s) {
      // show a few popular items by default
      render(items.slice(0, 6));
      return;
    }
    const res = items.filter(
      (x) =>
        x.name.toLowerCase().includes(s) || x.type.toLowerCase().includes(s)
    );
    render(res);
  }

  function select(i) {
    const choice = currentResults[i];
    if (!choice) return;
    input.value = choice.name;
    close();
    // TODO: navigate to the course/category page if you have URLs.
    // window.location.href = choice.url;
  }

  function move(delta) {
    if (!currentResults.length) return;
    activeIndex =
      (activeIndex + delta + currentResults.length) % currentResults.length;
    [...list.children].forEach((el, idx) => {
      el.setAttribute("aria-selected", String(idx === activeIndex));
      if (idx === activeIndex) {
        input.setAttribute("aria-activedescendant", el.id);
        el.scrollIntoView({ block: "nearest" });
      }
    });
  }

  // events
  input.addEventListener("focus", () => {
    open();
    filter(input.value);
  });
  input.addEventListener("input", () => {
    open();
    filter(input.value);
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "ArrowDown") {
      e.preventDefault();
      move(1);
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      move(-1);
    } else if (e.key === "Enter") {
      if (activeIndex >= 0 && currentResults[activeIndex]) {
        e.preventDefault();
        select(activeIndex);
      } else if (!currentResults.length) {
        window.location.href = addMissingUrl;
      }
    } else if (e.key === "Escape") {
      close();
    }
  });

  addMissing.addEventListener("click", () => {
    window.location.href = addMissingUrl;
  });

  document.addEventListener("click", (e) => {
    if (!wrap.contains(e.target)) close();
  });

  // initial state
  close();
})();
