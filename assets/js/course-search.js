(function () {
  const wrap = document.querySelector("#searchCourse");
  if (!wrap) return;

  const input = wrap.querySelector("#courseSearchInput");
  const popover = wrap.querySelector(".results-popover");
  const list = wrap.querySelector("#courseSearchList");
  const addMissing = wrap.querySelector(".add-missing");

  const addMissingUrl =
    window.courseSearchData?.addMissingUrl || "/add-missing-course/";
  const labels = window.courseSearchData?.labels || {
    course: "Course",
    category: "Category",
    noMatch: "Add missing course",
  };

  let activeIndex = -1;
  let currentResults = [];
  let timer;

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

  function render(results, term = "") {
    currentResults = results;
    list.innerHTML = "";

    const maxCourses = 4;
    const courses = results
      .filter((r) => r.type === "course")
      .slice(0, maxCourses);
    const categories = results.filter((r) => r.type === "category");

    if (courses.length) {
      addMissing.style.display = "none";

      courses.forEach((r, i) => {
        const li = document.createElement("li");
        li.className = "result-item";
        li.id = `cs-opt-${i}`;
        li.setAttribute("role", "option");
        li.setAttribute("aria-selected", "false");
        li.innerHTML = `
        <a href="${r.url}">
          <div class="result-name">${r.name}</div>
          <div class="result-type">${labels[r.type] || ""}</div>
        </a>
      `;
        list.appendChild(li);
      });
    }

    categories.forEach((r, i) => {
      const li = document.createElement("li");
      li.className = "result-item";
      li.id = `cs-opt-cat-${i}`;
      li.setAttribute("role", "option");
      li.setAttribute("aria-selected", "false");
      li.innerHTML = `
      <a href="${r.url}">
        <div class="result-name">${r.name}</div>
        <div class="result-type">${labels[r.type] || ""}</div>
      </a>
    `;
      list.appendChild(li);
    });

    if (term) {
      const li = document.createElement("li");
      li.className = "result-item search-option";
      li.id = `cs-search-for`;
      li.innerHTML = `
      <a href="${window.location.origin}/courses/?c=${encodeURIComponent(
        term
      )}">
        <div class="result-name"><ion-icon name="search-outline"></ion-icon> Search for "${term}"</div>
        <div class="result-type">Search all courses</div>
      </a>
    `;
      list.appendChild(li);
    }

    const addMissingItem = document.createElement("li");
    addMissingItem.className = "result-item add-missing-item";
    addMissingItem.innerHTML = `
    <button class="add-missing" type="button">
      <span class="plus">
        <ion-icon name="add-outline"></ion-icon>
      </span>
      <span class="add-missing-text">${labels.noMatch}</span>
    </button>
  `;
    list.appendChild(addMissingItem);
  }

  function fetchResults(q) {
    const search = q.trim();

    clearTimeout(timer);
    timer = setTimeout(() => {
      fetch(
        `${courseSearchData.ajaxUrl}?action=reviewmvp_course_search&nonce=${
          courseSearchData.nonce
        }&q=${encodeURIComponent(search)}`
      )
        .then((res) => res.json())
        .then((data) => {
          render(data, search);
        })
        .catch(() => render([], search));
    }, 300);
  }

  function select(i) {
    const choice = currentResults[i];
    if (!choice) return;
    input.value = choice.name;
    close();
    if (choice.url) window.location.href = choice.url;
  }

  function move(delta) {
    const items = [...list.children];
    if (!items.length) return;

    activeIndex = (activeIndex + delta + items.length) % items.length;

    items.forEach((el, idx) => {
      el.setAttribute("aria-selected", String(idx === activeIndex));
      if (idx === activeIndex) {
        input.setAttribute("aria-activedescendant", el.id);
        el.scrollIntoView({ block: "nearest" });
      }
    });
  }

  input.addEventListener("focus", () => {
    open();
    fetchResults(input.value);
  });

  input.addEventListener("input", () => {
    open();
    fetchResults(input.value);
  });

  input.addEventListener("keydown", (e) => {
    if (e.key === "ArrowDown") {
      e.preventDefault();
      move(1);
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      move(-1);
    } else if (e.key === "Enter") {
      e.preventDefault();
      const searchTerm = input.value.trim();

      if (activeIndex >= 0) {
        const link = list.children[activeIndex]?.querySelector("a");
        if (link) window.location.href = link.href;
      } else if (currentResults.length) {
        window.location.href = `${
          window.location.origin
        }/courses/?c=${encodeURIComponent(searchTerm)}`;
      } else {
        fetch(courseSearchData.ajaxUrl, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({
            action: "reviewmvp_save_no_match_term",
            nonce: courseSearchData.nonce,
            term: searchTerm,
          }),
        }).then(() => {
          window.location.href = `${window.location.origin}/no-course-found/`;
        });
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

  close();
})();
