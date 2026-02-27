document.addEventListener("DOMContentLoaded", () => {
  // ============================================================
  // HAMBURGER MENU FUNCTIONALITY
  // ============================================================
  const hamburgerBtn = document.getElementById("hamburger-menu");
  const sidebar = document.querySelector(".sidebar");
  const sidebarOverlay = document.getElementById("sidebar-overlay");

  if (hamburgerBtn && sidebar) {
    // Toggle menu on hamburger click
    hamburgerBtn.addEventListener("click", () => {
      hamburgerBtn.classList.toggle("active");
      sidebar.classList.toggle("active");
      sidebarOverlay.classList.toggle("active");
      document.body.style.overflow = hamburgerBtn.classList.contains("active")
        ? "hidden"
        : "";
    });

    // Close menu on overlay click
    if (sidebarOverlay) {
      sidebarOverlay.addEventListener("click", () => {
        hamburgerBtn.classList.remove("active");
        sidebar.classList.remove("active");
        sidebarOverlay.classList.remove("active");
        document.body.style.overflow = "";
      });
    }

    // Close menu when a category is clicked
    document.querySelectorAll(".cat-btn").forEach((btn) => {
      btn.addEventListener("click", () => {
        hamburgerBtn.classList.remove("active");
        sidebar.classList.remove("active");
        sidebarOverlay.classList.remove("active");
        document.body.style.overflow = "";
      });
    });

    // Close menu on window resize (when returning to desktop)
    window.addEventListener("resize", () => {
      if (window.innerWidth > 768) {
        hamburgerBtn.classList.remove("active");
        sidebar.classList.remove("active");
        sidebarOverlay.classList.remove("active");
        document.body.style.overflow = "";
      }
    });
  }

  // ============================================================
  // CATEGORY FILTERING (Original Code)
  // ============================================================
  // Export logic to window for setLang access
  window.currentCat = "all";
  window.searchQuery = "";
  window.activeFilters = { popular: false };

  window.runFilter = function (showSkeletons = true) {
    const grid = document.querySelector(".product-grid");
    if (!grid) return;

    const products = document.querySelectorAll(".product-card");
    const lang = localStorage.getItem("kiosk_lang") || "NL";

    // Hide current items
    products.forEach((p) => p.classList.add("hidden"));

    // Optional: Show skeletons
    if (showSkeletons) {
      for (let i = 0; i < 6; i++) {
        const skel = document.createElement("div");
        skel.className = "product-card skeleton";
        grid.appendChild(skel);
      }
    }

    const process = () => {
      grid.querySelectorAll(".skeleton").forEach((s) => s.remove());

      let visibleCount = 0;
      const q = (window.searchQuery || "").toLowerCase().trim();

      products.forEach((p) => {
        const pCat = p.getAttribute("data-category");
        const pName = (
          p.getAttribute("data-name-" + lang.toLowerCase()) || ""
        ).toLowerCase();
        const pDesc = (
          p.getAttribute("data-desc-" + lang.toLowerCase()) || ""
        ).toLowerCase();
        const pPopular = p.getAttribute("data-popular");

        const catMatch =
          window.currentCat === "all" || pCat == window.currentCat;
        const searchMatch = !q || pName.includes(q) || pDesc.includes(q);
        const dietMatch = !window.activeFilters.popular || pPopular === "1";

        if (catMatch && searchMatch && dietMatch) {
          p.classList.remove("hidden");
          visibleCount++;
        }
      });

      // Handle "No Results" msg
      let noResultMsg = document.getElementById("no-results-msg");
      if (visibleCount === 0) {
        if (!noResultMsg) {
          noResultMsg = document.createElement("div");
          noResultMsg.id = "no-results-msg";
          noResultMsg.className = "empty-msg";
          grid.appendChild(noResultMsg);
        }
        noResultMsg.innerText =
          lang === "EN"
            ? "No products found matching your search."
            : "Geen gerechten gevonden voor deze zoekopdracht.";
        noResultMsg.style.display = "block";
      } else if (noResultMsg) {
        noResultMsg.style.display = "none";
      }
    };

    if (showSkeletons) {
      setTimeout(process, 350);
    } else {
      process();
    }
  };

  // Category Buttons
  document.querySelectorAll(".cat-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document
        .querySelectorAll(".cat-btn")
        .forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");

      const lang = localStorage.getItem("kiosk_lang") || "NL";
      const nameEl = document.getElementById("active-cat-name");
      const descEl = document.getElementById("active-cat-desc");
      if (nameEl)
        nameEl.innerText = btn.getAttribute("data-" + lang.toLowerCase());
      if (descEl)
        descEl.innerText = btn.getAttribute("data-desc-" + lang.toLowerCase());

      window.currentCat = btn.dataset.id;
      window.runFilter(true);
    });
  });

  // Search Input
  const searchInput = document.getElementById("product-search");
  if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener("input", (e) => {
      clearTimeout(debounceTimer);
      window.searchQuery = e.target.value;
      debounceTimer = setTimeout(() => {
        window.runFilter(false);
      }, 100); // Shorter debounce for "on letter" feel
    });
  }

  // Filter Chips
  document.querySelectorAll(".filter-chip").forEach((chip) => {
    chip.addEventListener("click", () => {
      const filter = chip.dataset.filter;
      window.activeFilters[filter] = !window.activeFilters[filter];
      chip.classList.toggle("active", window.activeFilters[filter]);
      window.runFilter(true);
    });
  });

  // Set initial state
  window.runFilter(false);

  // Cart Logic
  window.cart = JSON.parse(localStorage.getItem("kiosk_cart")) || [];

  // Modal Logic
  const modal = document.getElementById("custom-modal");
  let currentProduct = null;
  let currentUpsell = null;
  let upsellActive = false;

  // Dynamic Customization Rules
  const customizationRules = [
    {
      keywords: ["avocado"],
      options: [
        { nl: "Extra Avocado", en: "Extra Avocado", price: 1.5 },
        { nl: "Geen Avocado", en: "No Avocado", price: 0 },
      ],
    },
    {
      keywords: ["feta", "kaas", "cheese", "halloumi"],
      options: [
        { nl: "Extra Kaas", en: "Extra Cheese", price: 1.25 },
        { nl: "Geen Kaas", en: "No Cheese", price: 0 },
      ],
    },
    {
      keywords: ["ei", "egg", "roerei"],
      options: [
        { nl: "Extra Ei", en: "Extra Egg", price: 1.0 },
        { nl: "Geen Ei", en: "No Egg", price: 0 },
      ],
    },
    {
      keywords: ["spinazie", "spinach"],
      options: [
        { nl: "Extra Spinazie", en: "Extra Spinach", price: 0.5 },
        { nl: "Geen Spinazie", en: "No Spinach", price: 0 },
      ],
    },
    {
      keywords: ["tofu"],
      options: [
        { nl: "Extra Tofu", en: "Extra Tofu", price: 2.0 },
        { nl: "Geen Tofu", en: "No Tofu", price: 0 },
      ],
    },
    {
      keywords: ["tempeh"],
      options: [
        { nl: "Extra Tempeh", en: "Extra Tempeh", price: 2.0 },
        { nl: "Geen Tempeh", en: "No Tempeh", price: 0 },
      ],
    },
    {
      keywords: ["falafel"],
      options: [
        { nl: "Extra Falafel", en: "Extra Falafel", price: 1.5 },
        { nl: "Geen Falafel", en: "No Falafel", price: 0 },
      ],
    },
    {
      keywords: ["granola", "haver", "oat"],
      options: [
        { nl: "Extra Granola/Haver", en: "Extra Granola/Oats", price: 0.75 },
        { nl: "Geen Granola", en: "No Granola", price: 0 },
      ],
    },
    {
      keywords: [
        "fruit",
        "bes",
        "berry",
        "aardbei",
        "strawberry",
        "banaan",
        "banana",
      ],
      options: [
        { nl: "Extra Fruit", en: "Extra Fruit", price: 1.25 },
        { nl: "Fruit Apart", en: "Fruit on the side", price: 0 },
      ],
    },
    {
      keywords: [
        "broccoli",
        "groente",
        "veggie",
        "edamame",
        "spinazie",
        "spinach",
      ],
      options: [
        { nl: "Extra Groenten", en: "Extra Veggies", price: 0.75 },
        { nl: "Geen Groenten", en: "No Veggies", price: 0 },
      ],
    },
    {
      keywords: ["ui", "uien", "onion"],
      options: [
        { nl: "Geen Ui", en: "No Onion", price: 0 },
        { nl: "Extra Ui", en: "Extra Onion", price: 0.3 },
      ],
    },
    {
      keywords: ["hummus"],
      options: [
        { nl: "Extra Hummus", en: "Extra Hummus", price: 0.75 },
        { nl: "Geen Hummus", en: "No Hummus", price: 0 },
      ],
    },
    {
      keywords: ["matcha"],
      options: [
        { nl: "Extra Shot Matcha", en: "Extra Matcha Shot", price: 1.0 },
      ],
    },
    {
      keywords: ["smoothie"],
      options: [
        { nl: "Extra Proteïne", en: "Extra Protein", price: 1.5 },
        { nl: "Geen Suiker", en: "No Sugar", price: 0 },
      ],
    },
    {
      keywords: ["kikkererwten", "chickpea"],
      options: [
        { nl: "Extra Kikkererwten", en: "Extra Chickpeas", price: 0.75 },
      ],
    },
    {
      keywords: ["pindakaas", "peanut"],
      options: [
        { nl: "Extra Pindakaas", en: "Extra Peanut Butter", price: 0.75 },
      ],
    },
    {
      keywords: ["quinoa", "rijst", "rice"],
      options: [{ nl: "Extra Basis", en: "Extra Base", price: 1.0 }],
    },
  ];

  window.openCustomizer = function (id) {
    currentProduct = window.allProducts.find((p) => p.product_id == id);
    if (!currentProduct) return;

    upsellActive = false;
    currentUpsell = currentProduct.cross_sell_id
      ? window.allProducts.find(
          (p) => p.product_id == currentProduct.cross_sell_id,
        )
      : null;

    const lang = localStorage.getItem("kiosk_lang") || "NL";

    // Set Main Info
    document.getElementById("modal-title").innerText =
      currentProduct["name" + (lang === "EN" ? "_en" : "")] ||
      currentProduct.name;
    document.getElementById("modal-desc").innerText =
      currentProduct["description" + (lang === "EN" ? "_en" : "")] ||
      currentProduct.description;
    document.getElementById("modal-img").src = window.productImages[id];

    // Generate Dynamic Extras
    const extrasGrid = document.getElementById("grid-extras");
    const wishesGrid = document.getElementById("grid-wishes");
    const extrasSection = document.getElementById("section-extras");
    const wishesSection = document.getElementById("section-wishes");

    extrasGrid.innerHTML = "";
    wishesGrid.innerHTML = "";

    const combinedText = (
      currentProduct.name +
      " " +
      (currentProduct.description || "") +
      " " +
      (currentProduct.name_en || "") +
      " " +
      (currentProduct.description_en || "")
    ).toLowerCase();

    let extrasCount = 0;
    let wishesCount = 0;

    // Detect if product is Savory or Sweet based on category
    const catName =
      (window.allCategories &&
        window.allCategories[currentProduct.category_id]) ||
      "";
    const isSweetCat = [
      "Breakfast",
      "Drinks",
      "Ontbijt",
      "Dranken",
      "Smoothies",
    ].includes(catName);
    const isSavory =
      !isSweetCat ||
      combinedText.includes("tofu") ||
      combinedText.includes("falafel") ||
      combinedText.includes("wrap") ||
      combinedText.includes("toastie") ||
      combinedText.includes("burger");

    customizationRules.forEach((rule) => {
      // Use Regex for exact word matching to avoid "fruit" matching "ui"
      const match = rule.keywords.some((k) => {
        const regex = new RegExp("\\b" + k + "\\b", "i");
        return regex.test(combinedText);
      });

      if (match) {
        rule.options.forEach((opt) => {
          const label = document.createElement("label");
          label.className = "extra-item";
          const name = lang === "EN" ? opt.en : opt.nl;
          const priceText = opt.price > 0 ? ` (+€${opt.price.toFixed(2)})` : "";
          label.innerHTML = `
            <input type="checkbox" data-name="${name}" data-price="${opt.price}">
            <span>${name}${priceText}</span>
          `;
          label
            .querySelector("input")
            .addEventListener("change", updateModalTotal);

          if (opt.price > 0) {
            extrasGrid.appendChild(label);
            extrasCount++;
          } else {
            wishesGrid.appendChild(label);
            wishesCount++;
          }
        });
      }
    });

    // Add generic options based on type
    const savoryGenerics = [
      { nl: "Maak Pittig", en: "Make Spicy", price: 0 },
      { nl: "Saus Apart", en: "Sauce on side", price: 0 },
    ];

    const sweetGenerics = [
      { nl: "Extra Bestek", en: "Extra Cutlery", price: 0 },
      { nl: "Geen Suiker", en: "No Sugar", price: 0 },
    ];

    const relevantGenerics = isSavory ? savoryGenerics : sweetGenerics;

    relevantGenerics.forEach((opt) => {
      const label = document.createElement("label");
      label.className = "extra-item";
      const name = lang === "EN" ? opt.en : opt.nl;
      label.innerHTML = `
        <input type="checkbox" data-name="${name}" data-price="${opt.price}">
        <span>${name}</span>
      `;
      label.querySelector("input").addEventListener("change", updateModalTotal);
      wishesGrid.appendChild(label);
      wishesCount++;
    });

    // Inject Spacer after wishes
    const spacer = document.createElement("div");
    spacer.className = "scroll-spacer";
    wishesGrid.appendChild(spacer);

    // Toggle sections visibility
    extrasSection.classList.toggle("hidden", extrasCount === 0);
    wishesSection.classList.toggle("hidden", wishesCount === 0);

    // Update Titles
    document.getElementById("modal-extras-title").innerText =
      lang === "EN" ? "Extra's" : "Extra's";
    document.getElementById("modal-wishes-title").innerText =
      lang === "EN" ? "Wishes" : "Wensen";
    document.getElementById("modal-confirm-btn").innerText =
      lang === "EN" ? "Add to Order" : "Toevoegen aan bestelling";

    // Setup Upsell
    const upsellBox = document.getElementById("upsell-container");
    if (currentUpsell) {
      upsellBox.classList.remove("hidden");
      document.getElementById("upsell-name").innerText =
        currentUpsell["name" + (lang === "EN" ? "_en" : "")] ||
        currentUpsell.name;
      document.getElementById("upsell-price").innerText =
        "€" + parseFloat(currentUpsell.price).toFixed(2);
      document.getElementById("upsell-img").src =
        window.productImages[currentUpsell.product_id];
      updateUpsellBtnText(lang);
    } else {
      upsellBox.classList.add("hidden");
    }

    updateModalTotal();

    // Reset view to customizer
    document.getElementById("customizer-view").classList.remove("hidden");
    document.getElementById("upsell-view").classList.add("hidden");
    document.querySelector(".close-modal").classList.remove("hidden");

    modal.classList.remove("hidden");
  };

  window.closeCustomizer = function () {
    modal.classList.add("hidden");
  };

  window.toggleUpsell = function () {
    upsellActive = !upsellActive;
    const lang = localStorage.getItem("kiosk_lang") || "NL";
    updateUpsellBtnText(lang);
    updateModalTotal();
  };

  function updateUpsellBtnText(lang) {
    const btn = document.getElementById("upsell-btn-action");
    if (upsellActive) {
      btn.innerText = lang === "EN" ? "Remove from deal" : "Verwijder uit deal";
      btn.style.background = "#666";
    } else {
      btn.innerText = lang === "EN" ? "Add to order" : "Voeg toe voor deal";
      btn.style.background = "var(--color-orange)";
    }
  }

  function updateModalTotal() {
    let total = parseFloat(currentProduct.price);
    if (upsellActive) total += parseFloat(currentUpsell.price);

    document.querySelectorAll(".extra-item input:checked").forEach((i) => {
      total += parseFloat(i.dataset.price);
    });

    document.getElementById("modal-total-price").innerText =
      "€" + total.toFixed(2);
  }

  // Listen for extra changes
  document.querySelectorAll(".extra-item input").forEach((input) => {
    input.addEventListener("change", updateModalTotal);
  });

  window.confirmCustomization = function () {
    const extras = [];
    document.querySelectorAll(".extra-item input:checked").forEach((i) => {
      extras.push({ name: i.dataset.name, price: parseFloat(i.dataset.price) });
    });

    // Trigger Fly Animation
    const modalImg = document.getElementById("modal-img");
    const cartTarget = document.querySelector(".order-panel");
    if (modalImg && cartTarget && modalImg.src) {
      animateFlyToCart(modalImg, cartTarget);
    }

    addToCart(currentProduct, extras);

    // Add upsell if active (from the inline version)
    if (upsellActive && currentUpsell) {
      addToCart(currentUpsell);
      upsellActive = false; // Reset
    }

    // Instead of closing, show the new Upsell Screen
    showUpsellScreen();
  };

  function showUpsellScreen() {
    const lang = localStorage.getItem("kiosk_lang") || "NL";
    const upsellView = document.getElementById("upsell-view");
    const customizerView = document.getElementById("customizer-view");

    // Hide customizer, show upsell
    customizerView.classList.add("hidden");
    upsellView.classList.remove("hidden");
    document.querySelector(".close-modal").classList.add("hidden"); // Force choice

    // Logic to pick suggestions
    const suggestionsGrid = document.getElementById("upsell-suggestions-grid");
    suggestionsGrid.innerHTML = "";

    // Pick 2 items from different categories (Drinks/Snacks)
    const drinks = window.allProducts
      .filter((p) => p.category_id == 6 || p.category_id == 7)
      .slice(0, 4);
    const snacks = window.allProducts
      .filter((p) => p.category_id == 5)
      .slice(0, 4);

    // Shuffle and pick 2
    const pool = [...drinks, ...snacks]
      .sort(() => 0.5 - Math.random())
      .slice(0, 2);

    pool.forEach((p) => {
      const card = document.createElement("div");
      card.className = "upsell-option-card";
      const name = p["name" + (lang === "EN" ? "_en" : "")] || p.name;
      const img = window.productImages[p.product_id] || "";

      card.innerHTML = `
        <img src="${img}" alt="${name}">
        <h4>${name}</h4>
        <p>€${parseFloat(p.price).toFixed(2)}</p>
        <button onclick="addUpsellAndClose(${p.product_id})">${lang === "EN" ? "Add" : "Voeg toe"}</button>
      `;
      suggestionsGrid.appendChild(card);
    });

    // Update labels
    document.getElementById("upsell-view-title").innerText =
      lang === "EN" ? "Nice to add?" : "Lekker voor erbij?";
    document.getElementById("upsell-skip-btn").innerText =
      lang === "EN" ? "No thanks, continue" : "Nee bedankt, verder gaan";
  }

  window.addUpsellAndClose = function (productId) {
    const product = window.allProducts.find((p) => p.product_id == productId);
    if (product) {
      addToCart(product);
      // Small delay for feedback
      setTimeout(closeCustomizer, 300);
    }
  };

  window.skipUpsell = function () {
    closeCustomizer();
  };

  window.addToCart = function (product, extras = []) {
    const extraKey = extras
      .map((e) => e.name)
      .sort()
      .join(",");
    const cartId = product.product_id + (extraKey ? "-" + extraKey : "");

    const existing = window.cart.find((item) => item.cartId === cartId);
    if (existing) {
      existing.quantity++;
    } else {
      let finalPrice = parseFloat(product.price);
      extras.forEach((e) => (finalPrice += e.price));

      window.cart.push({
        cartId: cartId,
        product_id: product.product_id,
        name: product.name,
        name_en: product.name_en || product.name,
        price: finalPrice,
        basePrice: parseFloat(product.price),
        quantity: 1,
        extras: extras,
      });
    }
    saveCart();
    updateCartUI();
  };

  window.removeFromCart = function (cartId) {
    window.cart = window.cart.filter((item) => item.cartId !== cartId);
    saveCart();
    updateCartUI();
  };

  window.updateQty = function (cartId, change) {
    const item = window.cart.find((i) => i.cartId === cartId);
    if (item) {
      item.quantity += change;
      if (item.quantity <= 0) {
        removeFromCart(cartId);
      } else {
        saveCart();
        updateCartUI();
      }
    }
  };

  window.saveCart = function () {
    localStorage.setItem("kiosk_cart", JSON.stringify(window.cart));
  };

  window.updateCartUI = function () {
    const container = document.getElementById("cart-items");
    const totalEl = document.getElementById("cart-total");
    const checkoutBtn = document.getElementById("checkout-btn");

    container.innerHTML = "";
    let total = 0;

    const lang = localStorage.getItem("kiosk_lang") || "NL";

    if (window.cart.length === 0) {
      container.innerHTML = "";
      checkoutBtn.disabled = true;
    } else {
      checkoutBtn.disabled = false;
      window.cart.forEach((item) => {
        total += item.price * item.quantity;

        const div = document.createElement("div");
        div.className = "cart-item";

        let extrasHtml = "";
        if (item.extras && item.extras.length > 0) {
          extrasHtml = `<div class="cart-item-extras">${item.extras.map((e) => "+ " + e.name).join("<br>")}</div>`;
        }

        const displayName = lang === "EN" ? item.name_en : item.name;

        div.innerHTML = `
                  <div class="cart-item-info">
                      <span class="cart-item-title">${displayName}</span>
                      ${extrasHtml}
                      <span class="cart-item-price">€${item.price.toFixed(2)}</span>
                  </div>
                  <div class="cart-controls">
                      <button class="qty-btn" onclick="updateQty('${item.cartId}', -1)">-</button>
                      <span>${item.quantity}</span>
                      <button class="qty-btn" onclick="updateQty('${item.cartId}', 1)">+</button>
                  </div>
              `;
        container.appendChild(div);
      });
    }

    totalEl.innerText = "€" + total.toFixed(2);
  };

  function animateFlyToCart(sourceImg, targetEl) {
    const flyingImg = sourceImg.cloneNode();
    const sourceRect = sourceImg.getBoundingClientRect();
    const targetRect = targetEl.getBoundingClientRect();

    flyingImg.classList.add("flying-item");
    flyingImg.style.width = sourceRect.width + "px";
    flyingImg.style.height = sourceRect.height + "px";
    flyingImg.style.top = sourceRect.top + "px";
    flyingImg.style.left = sourceRect.left + "px";

    document.body.appendChild(flyingImg);

    // Force reflow
    flyingImg.offsetWidth;

    // Animate to cart
    flyingImg.style.top = targetRect.top + 20 + "px";
    flyingImg.style.left = targetRect.left + targetRect.width / 2 + "px";
    flyingImg.style.width = "40px";
    flyingImg.style.height = "40px";
    flyingImg.style.opacity = "0";

    setTimeout(() => {
      flyingImg.remove();
      // Trigger cart bounce
      const cartPanel = document.querySelector(".order-panel");
      cartPanel.classList.add("cart-bounce");
      setTimeout(() => cartPanel.classList.remove("cart-bounce"), 400);
    }, 800);
  }

  updateCartUI();
});
