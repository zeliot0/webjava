console.log("TASK UI JS LOADED ✅");

(() => {
  // =========================
  // THEME
  // =========================
  const themeBtn = document.getElementById("themeToggle");
  const html = document.documentElement;

  function setTheme(theme) {
    html.setAttribute("data-theme", theme);
    localStorage.setItem("theme", theme);
    if (themeBtn) {
      themeBtn.innerHTML =
        theme === "dark"
          ? '<i class="fas fa-moon"></i>'
          : '<i class="fas fa-sun"></i>';
    }
  }

  setTheme(localStorage.getItem("theme") || "light");
  themeBtn?.addEventListener("click", () => {
    const cur = html.getAttribute("data-theme") || "light";
    setTheme(cur === "dark" ? "light" : "dark");
  });

  // =========================
  // API
  // =========================
  const api = window.NEXA_API;
  if (!api?.list || !api?.create || !api?.updateBase || !api?.deleteBase) {
    console.error("window.NEXA_API missing/invalid ❌", api);
    return;
  }

  // =========================
  // UI refs
  // =========================
  const zones = {
    todo: document.getElementById("zone-todo"),
    doing: document.getElementById("zone-doing"),
    done: document.getElementById("zone-done"),
  };

  const overlay = document.getElementById("overlay");
  const drawer = document.getElementById("drawer");

  const newBtn = document.getElementById("newBtn");
  const closeDrawerBtn = document.getElementById("closeDrawer");
  const cancelBtn = document.getElementById("cancelBtn");
  const saveBtn = document.getElementById("saveBtn");
  const deleteBtn = document.getElementById("deleteBtn");
  const editBtn = document.getElementById("editBtn");

  const fTitle = document.getElementById("fTitle");
  const fDesc = document.getElementById("fDesc");
  const fPrio = document.getElementById("fPrio");
  const fStatus = document.getElementById("fStatus");
  const fDue = document.getElementById("fDue");
  const fCategory = document.getElementById("fCategory");

  // Toolbar filters
  const searchInput = document.getElementById("searchInput");
  const prioFilter = document.getElementById("prioFilter");
  const statusFilter = document.getElementById("statusFilter");
  const clearBtn = document.getElementById("clearBtn");

  // KPI
  const kpiTotal = document.getElementById("kpiTotal");
  const kpiDoing = document.getElementById("kpiDoing");
  const kpiHigh = document.getElementById("kpiHigh");

  // Column counts
  const countTodo = document.getElementById("countTodo");
  const countDoing = document.getElementById("countDoing");
  const countDone = document.getElementById("countDone");

  // Sidebar (task bar left)
  const sidebar = document.getElementById("sidebar");
  const sbOverlay = document.getElementById("sbOverlay");
  const sbCollapse = document.getElementById("sbCollapse");
  const topSidebarBtn = document.getElementById("topSidebarBtn");
  const sbTasksCount = document.getElementById("sbTasksCount");

  // =========================
  // CATEGORY DRAWER refs (top button)
  // =========================
  const newCatBtn = document.getElementById("newCatBtn");
  const catOverlay = document.getElementById("catOverlay");
  const catDrawer = document.getElementById("catDrawer");
  const closeCatDrawer = document.getElementById("closeCatDrawer");
  const cancelCatBtn = document.getElementById("cancelCatBtn");
  const saveCatBtn = document.getElementById("saveCatBtn");

  const cat_name = document.getElementById("cat_name");
  const cat_description = document.getElementById("cat_description");
  const cat_color = document.getElementById("cat_color");
  const cat_icon = document.getElementById("cat_icon");
  const cat_isActive = document.getElementById("cat_isActive");
  const cat_position = document.getElementById("cat_position");
  const cat_visibility = document.getElementById("cat_visibility");
  const cat_taskLimit = document.getElementById("cat_taskLimit");
  const cat_createAt = document.getElementById("cat_createAt");
  const cat_updateAt = document.getElementById("cat_updateAt");
  const cat_no = document.getElementById("cat_no");

  // =========================
  // STATE
  // =========================
  let editingId = null;
  let allTasks = [];
  let categories = [];

  // =========================
  // HTTP
  // =========================
  async function http(url, method, body) {
    const res = await fetch(url, {
      method,
      headers: { "Content-Type": "application/json" },
      body: body ? JSON.stringify(body) : undefined,
    });

    const text = await res.text();
    let data = null;
    try {
      data = text ? JSON.parse(text) : null;
    } catch (_) {}

    if (!res.ok) {
      console.error("API ERROR:", method, url, res.status, data || text);
      alert(`Erreur API ${res.status}: ` + (data?.error ?? text));
      throw new Error(data?.error ?? text);
    }
    return data;
  }

  function escapeHtml(s) {
    return String(s ?? "").replace(/[&<>"']/g, (c) => ({
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    }[c]));
  }

  // =========================
  // ✅ VOICE SEARCH (FIXED)
  // =========================
  const voiceBtn = document.getElementById("voiceBtn");
  console.log("VOICE DEBUG:", {
  voiceBtnFound: !!document.getElementById("voiceBtn"),
  searchInputFound: !!document.getElementById("searchInput"),
  SpeechRecognitionExists: !!(window.SpeechRecognition || window.webkitSpeechRecognition),
  isSecureContext: window.isSecureContext,
  protocol: location.protocol,
  host: location.host
});

// Petit banner visuel si bloqué
(function showVoiceBannerIfBlocked() {
  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SR) {
    console.warn("❌ SpeechRecognition not supported by this browser.");
    return;
  }
  if (!window.isSecureContext) {
    console.warn("❌ Not a secure context. Voice will be blocked.");
    const div = document.createElement("div");
    div.style.cssText =
      "position:fixed;bottom:16px;left:16px;right:16px;z-index:99999;padding:12px 14px;border-radius:12px;background:#fff;border:1px solid rgba(0,0,0,.2);box-shadow:0 8px 30px rgba(0,0,0,.12);font-family:system-ui;";
    div.innerHTML =
      "🎤 <b>Recherche vocale bloquée</b> : ouvre l’app en <b>HTTPS</b> ou <b>http://localhost</b> (sinon Chrome bloque le micro).";
    document.body.appendChild(div);
  }
})();


  function initVoiceSearch() {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!voiceBtn) {
      console.warn("voiceBtn not found");
      return null;
    }

    if (!SR) {
      console.warn("SpeechRecognition not supported.");
      voiceBtn.style.display = "none";
      return null;
    }

    const recog = new SR();
    recog.lang = "fr-FR";
    recog.interimResults = true;
    recog.continuous = false;

    let listening = false;

    const setUI = (on) => {
      listening = on;
      voiceBtn.classList.toggle("listening", on);
      voiceBtn.innerHTML = on
        ? '<i class="fa-solid fa-microphone-lines"></i>'
        : '<i class="fa-solid fa-microphone"></i>';
    };

    recog.onstart = () => {
      console.log("🎤 Voice START");
      setUI(true);
    };

    recog.onresult = (e) => {
      let transcript = "";
      for (let i = e.resultIndex; i < e.results.length; i++) {
        transcript += e.results[i][0].transcript;
      }
      transcript = transcript.trim();
      console.log("🎤 Transcript:", transcript);

      if (searchInput) {
        searchInput.value = transcript;
        render();
      }
    };

    recog.onerror = (e) => {
      console.error("🎤 Voice ERROR:", e.error, e.message || "");
      setUI(false);

      if (e.error === "not-allowed" || e.error === "service-not-allowed") {
        alert("⚠️ Autorise le micro (icône 🔒 à gauche de l’URL) puis recharge la page.");
      } else if (e.error === "network") {
        alert("⚠️ Problème réseau / service. Essaie Chrome + HTTPS/localhost.");
      }
    };

    recog.onend = () => {
      console.log("🎤 Voice END");
      setUI(false);
    };

    return {
      toggle: () => {
        try {
          if (!listening) recog.start();
          else recog.stop();
        } catch (err) {
          console.error("🎤 start/stop error:", err);
          alert("⚠️ Impossible de démarrer la voix. Vérifie HTTPS/permission micro.");
          setUI(false);
        }
      }
    };
  }

  // =========================
  // SIDEBAR FIX (NO MOVE)
  // =========================
  function initSidebar() {
    if (!sidebar) return;

    sidebar.style.top = "0";
    sidebar.style.height = "100vh";

    const KEY = "nexa_sidebar_state";
    const getState = () => {
      try { return JSON.parse(localStorage.getItem(KEY) || '{"mode":"open"}'); }
      catch { return { mode: "open" }; }
    };
    const setState = (mode) => {
      localStorage.setItem(KEY, JSON.stringify({ mode }));
      applyState();
    };
    const applyState = () => {
      const st = getState();
      sidebar.classList.remove("is-collapsed", "is-hidden");
      if (st.mode === "collapsed") sidebar.classList.add("is-collapsed");
      if (st.mode === "hidden") sidebar.classList.add("is-hidden");
    };

    sbCollapse?.addEventListener("click", () => {
      const st = getState();
      setState(st.mode === "open" ? "collapsed" : "open");
    });

    topSidebarBtn?.addEventListener("click", () => {
      const st = getState();
      setState(st.mode === "hidden" ? "open" : "hidden");
    });

    const openMobile = () => {
      sidebar.classList.add("open");
      sbOverlay?.classList.add("open");
    };
    const closeMobile = () => {
      sidebar.classList.remove("open");
      sbOverlay?.classList.remove("open");
    };
    sbOverlay?.addEventListener("click", closeMobile);
    topSidebarBtn?.addEventListener("dblclick", openMobile);

    applyState();
  }

  function setSidebarCount(n) {
    if (sbTasksCount) sbTasksCount.textContent = String(n ?? 0);
  }

  // =========================
  // DRAWER (Task)
  // =========================
  function openDrawer(task = null) {
    overlay?.classList.add("open");
    drawer?.classList.add("open");

    if (!task) {
      editingId = null;
      deleteBtn && (deleteBtn.style.display = "none");
      editBtn && (editBtn.style.display = "none");

      fTitle.value = "";
      fDesc.value = "";
      fPrio.value = "med";
      fStatus.value = "todo";
      fDue.value = "";
      fCategory.value = "";
      return;
    }

    editingId = task.id;
    deleteBtn && (deleteBtn.style.display = "inline-flex");
    editBtn && (editBtn.style.display = "inline-flex");

    fTitle.value = task.title || "";
    fDesc.value = task.description || "";
    fPrio.value = task.priority || "med";
    fStatus.value = task.status || "todo";
    fDue.value = task.dueAt || "";
    fCategory.value = task.categoryId ?? "";
  }

  function closeDrawer() {
    overlay?.classList.remove("open");
    drawer?.classList.remove("open");
  }

  function getPayload() {
    return {
      title: (fTitle.value || "").trim(),
      description: (fDesc.value || "").trim() || null,
      priority: fPrio.value || "med",
      status: fStatus.value || "todo",
      dueAt: fDue.value || null,
      categoryId: fCategory.value ? parseInt(fCategory.value, 10) : null,
    };
  }

  async function saveOrUpdate() {
    const payload = getPayload();

    if (!payload.title) {
      alert("Le titre est obligatoire");
      return;
    }

    if (!editingId) {
      await http(api.create, "POST", payload);
    } else {
      await http(`${api.updateBase}/${editingId}`, "PATCH", payload);
    }

    closeDrawer();
    await refreshTasks();
    render();
  }

  async function deleteCurrent() {
    if (!editingId) return;
    const ok = confirm("Supprimer cette tâche ?");
    if (!ok) return;
    await http(`${api.deleteBase}/${editingId}`, "DELETE");
    closeDrawer();
    await refreshTasks();
    render();
  }

  // =========================
  // RENDER helpers
  // =========================
  function clearZones() {
    Object.values(zones).forEach((z) => z && (z.innerHTML = ""));
  }

  function prioLabel(p) {
    if (p === "high") return "Haute";
    if (p === "med") return "Moyenne";
    return "Basse";
  }

  function categoryPill(cat) {
    if (!cat) return "";
    const color = cat.color || "#7c3aed";
    const icon = cat.icon || "fa-solid fa-folder";
    const name = cat.name || "Catégorie";

    return `
      <span class="cat-pill" style="--cat:${escapeHtml(color)}">
        <i class="${escapeHtml(icon)}"></i>
        <span>${escapeHtml(name)}</span>
      </span>
    `;
  }

  function taskCard(task) {
    const el = document.createElement("div");
    el.className = "task";
    el.draggable = true;
    el.dataset.id = String(task.id);

    const cat =
      categories.find((c) => String(c.id) === String(task.categoryId)) ||
      (task.categoryName
        ? { name: task.categoryName, color: task.categoryColor, icon: task.categoryIcon }
        : null);

    el.innerHTML = `
      <div class="task-top">
        <div style="min-width:0;">
          <h4 class="task-title">${escapeHtml(task.title)}</h4>
        </div>
        <div class="task-actions">
          <button class="icon-btn task-edit" type="button" title="Modifier">
            <i class="fa-regular fa-pen-to-square"></i>
          </button>
          <button class="icon-btn task-del" type="button" title="Supprimer">
            <i class="fa-regular fa-trash-can"></i>
          </button>
        </div>
      </div>

      ${task.description ? `<div class="task-desc">${escapeHtml(task.description)}</div>` : ""}

      <div class="chips">
        <span class="chip prio ${escapeHtml(task.priority || "med")}">
          <i class="fa-solid fa-flag"></i> ${prioLabel(task.priority || "med")}
        </span>

        ${task.dueAt ? `<span class="chip"><i class="fa-regular fa-calendar"></i> ${escapeHtml(task.dueAt)}</span>` : ""}

        ${cat ? categoryPill(cat) : ""}
      </div>
    `;

    el.querySelector(".task-edit")?.addEventListener("click", (e) => {
      e.stopPropagation();
      openDrawer(task);
    });

    el.querySelector(".task-del")?.addEventListener("click", async (e) => {
      e.stopPropagation();
      const ok = confirm(`Supprimer la tâche : "${task.title}" ?`);
      if (!ok) return;
      await http(`${api.deleteBase}/${task.id}`, "DELETE");
      await refreshTasks();
      render();
    });

    el.addEventListener("dblclick", () => openDrawer(task));
    el.addEventListener("dragstart", (e) => {
      e.dataTransfer.setData("text/plain", String(task.id));
      setTimeout(() => (el.style.opacity = "0.65"), 0);
    });
    el.addEventListener("dragend", () => (el.style.opacity = "1"));

    return el;
  }

  function applyFilters(tasks) {
    const q = (searchInput?.value || "").trim().toLowerCase();
    const pf = prioFilter?.value || "all";
    const sf = statusFilter?.value || "all";

    return tasks.filter((t) => {
      const hay = `${t.title || ""} ${t.description || ""}`.toLowerCase();
      const okSearch = !q || hay.includes(q);
      const okPrio = pf === "all" || (t.priority || "med") === pf;
      const okStatus = sf === "all" || (t.status || "todo") === sf;
      return okSearch && okPrio && okStatus;
    });
  }

  function updateKPIs() {
    const total = allTasks.length;
    const doing = allTasks.filter((t) => t.status === "doing").length;
    const high = allTasks.filter((t) => t.priority === "high").length;

    if (kpiTotal) kpiTotal.textContent = String(total);
    if (kpiDoing) kpiDoing.textContent = String(doing);
    if (kpiHigh) kpiHigh.textContent = String(high);

    if (countTodo) countTodo.textContent = String(allTasks.filter((t) => t.status === "todo").length);
    if (countDoing) countDoing.textContent = String(doing);
    if (countDone) countDone.textContent = String(allTasks.filter((t) => t.status === "done").length);

    setSidebarCount(total);
  }

  function render() {
    updateKPIs();
    clearZones();

    const filtered = applyFilters(allTasks);
    filtered.forEach((t) => {
      const st = t.status || "todo";
      (zones[st] || zones.todo).appendChild(taskCard(t));
    });
  }

  // =========================
  // LOAD
  // =========================
  async function loadCategories(selectValue = null) {
    try {
      categories = await http(api.categories, "GET");
    } catch {
      categories = [];
    }

    if (fCategory) {
      const keep = selectValue ?? fCategory.value ?? "";
      fCategory.innerHTML = `<option value="">Aucune catégorie</option>`;
      categories.forEach((c) => {
        const opt = document.createElement("option");
        opt.value = String(c.id);
        opt.textContent = c.name;
        fCategory.appendChild(opt);
      });
      if (keep) fCategory.value = keep;
    }
  }

  async function refreshTasks() {
    allTasks = await http(api.list, "GET");
  }

  // =========================
  // DRAG & DROP (PATCH status only)
  // =========================
  function initDragDrop() {
    Object.entries(zones).forEach(([status, zone]) => {
      if (!zone) return;

      zone.addEventListener("dragover", (e) => e.preventDefault());
      zone.addEventListener("drop", async (e) => {
        e.preventDefault();
        const id = e.dataTransfer.getData("text/plain");
        if (!id) return;

        await http(`${api.updateBase}/${id}`, "PATCH", { status });
        await refreshTasks();
        render();
      });
    });
  }

  // =========================
  // CATEGORY DRAWER (top button)
  // =========================
  function openCatDrawer() {
    catOverlay?.classList.add("open");
    catDrawer?.classList.add("open");
  }

  function closeCatDrawerFn() {
    catOverlay?.classList.remove("open");
    catDrawer?.classList.remove("open");

    if (cat_name) cat_name.value = "";
    if (cat_description) cat_description.value = "";
    if (cat_color) cat_color.value = "#7c3aed";
    if (cat_icon) cat_icon.value = "";
    if (cat_isActive) cat_isActive.value = "1";
    if (cat_position) cat_position.value = "";
    if (cat_visibility) cat_visibility.value = "";
    if (cat_taskLimit) cat_taskLimit.value = "";
    if (cat_createAt) cat_createAt.value = "";
    if (cat_updateAt) cat_updateAt.value = "";
    if (cat_no) cat_no.value = "";
  }

  function toIsoFromDatetimeLocal(v) {
    if (!v) return null;
    return v.replace("T", " ") + ":00";
  }

  async function createCategory() {
    const name = (cat_name?.value || "").trim();
    if (!name) {
      alert("Nom catégorie obligatoire");
      return;
    }

    const payload = {
      name,
      description: (cat_description?.value || "").trim() || null,
      color: cat_color?.value || null,
      icon: (cat_icon?.value || "").trim() || null,
      isActive: (cat_isActive?.value || "1") === "1",
      position: cat_position?.value ? parseInt(cat_position.value, 10) : null,
      visibility: (cat_visibility?.value || "").trim() || null,
      taskLimit: cat_taskLimit?.value ? parseInt(cat_taskLimit.value, 10) : null,
      createAt: toIsoFromDatetimeLocal(cat_createAt?.value),
      updateAt: toIsoFromDatetimeLocal(cat_updateAt?.value),
      no: (cat_no?.value || "").trim() || null,
    };

    const url = api.categoriesCreate || "/api/categories";
    const created = await http(url, "POST", payload);

    const newCat = {
      id: created.id,
      name: created.name,
      color: created.color || payload.color || null,
      icon: created.icon || payload.icon || null,
    };

    categories = categories.filter((c) => String(c.id) !== String(newCat.id));
    categories.unshift(newCat);

    await loadCategories(String(newCat.id));

    closeCatDrawerFn();
    render();
  }

  // =========================
  // EVENTS
  // =========================
  function bindEvents() {
    newBtn?.addEventListener("click", () => openDrawer(null));
    closeDrawerBtn?.addEventListener("click", closeDrawer);
    cancelBtn?.addEventListener("click", closeDrawer);
    overlay?.addEventListener("click", closeDrawer);

    saveBtn?.addEventListener("click", saveOrUpdate);
    editBtn?.addEventListener("click", saveOrUpdate);
    deleteBtn?.addEventListener("click", deleteCurrent);

    // Filters
    searchInput?.addEventListener("input", render);
    prioFilter?.addEventListener("change", render);
    statusFilter?.addEventListener("change", render);

    // ✅ Voice bind
    const voice = initVoiceSearch();
    voiceBtn?.addEventListener("click", () => {
      if (!voice) return;
      voice.toggle();
    });

    clearBtn?.addEventListener("click", () => {
      if (searchInput) searchInput.value = "";
      if (prioFilter) prioFilter.value = "all";
      if (statusFilter) statusFilter.value = "all";
      render();
    });

    // ESC close
    window.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && drawer?.classList.contains("open")) closeDrawer();
      if (e.key === "Escape" && catDrawer?.classList.contains("open")) closeCatDrawerFn();
    });

    // category drawer
    newCatBtn?.addEventListener("click", openCatDrawer);
    catOverlay?.addEventListener("click", closeCatDrawerFn);
    closeCatDrawer?.addEventListener("click", closeCatDrawerFn);
    cancelCatBtn?.addEventListener("click", closeCatDrawerFn);
    saveCatBtn?.addEventListener("click", () => createCategory().catch(console.error));
  }

  // =========================
  // INIT
  // =========================
  (async function init() {
    initSidebar();
    bindEvents();
    initDragDrop();

    await loadCategories();
    await refreshTasks();
    render();
  })().catch((err) => console.error("INIT ERROR:", err));
})();
