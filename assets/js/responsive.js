/* Responsive Utilities and Helpers */

/**
 * Add responsive helper class to track current breakpoint
 * Usage: document.body.classList.contains('mobile') for mobile detection
 */
function initResponsiveHelper() {
  const updateBreakpoint = () => {
    const width = window.innerWidth;
    document.body.classList.remove(
      "mobile",
      "tablet",
      "desktop",
      "large-desktop",
    );

    if (width < 480) {
      document.body.classList.add("mobile");
    } else if (width < 768) {
      document.body.classList.add("mobile");
    } else if (width < 1024) {
      document.body.classList.add("tablet");
    } else if (width < 1440) {
      document.body.classList.add("desktop");
    } else {
      document.body.classList.add("large-desktop");
    }
  };

  updateBreakpoint();
  window.addEventListener("resize", updateBreakpoint);
}

/**
 * Detect if device is touch-enabled
 */
function isTouchDevice() {
  return (
    (typeof window !== "undefined" &&
      ("ontouchstart" in window ||
        (typeof window.DocumentTouch !== "undefined" &&
          document instanceof window.DocumentTouch))) ||
    (typeof navigator !== "undefined" &&
      (navigator.maxTouchPoints > 0 || navigator.msMaxTouchPoints > 0))
  );
}

/**
 * Get viewport dimensions accounting for mobile nav/address bar
 */
function getViewportHeight() {
  // Use visualViewport if available (better for mobile with nav bars)
  if (typeof window.visualViewport !== "undefined") {
    return window.visualViewport.height;
  }
  return window.innerHeight;
}

/**
 * Adaptive grid layout helper
 * Automatically adjusts grid columns based on container width
 */
function initAdaptiveGrid() {
  const grids = document.querySelectorAll(
    ".products-grid, .upsell-suggestions-grid",
  );

  const updateGridCols = () => {
    grids.forEach((grid) => {
      const width = grid.offsetWidth;
      let cols = 3;

      if (width < 480) {
        cols = 1;
      } else if (width < 768) {
        cols = 2;
      } else if (width < 1024) {
        cols = 3;
      } else if (width < 1440) {
        cols = 4;
      } else {
        cols = 5;
      }

      // Update CSS custom property for grid layout
      grid.style.setProperty("--grid-cols", cols);
    });
  };

  updateGridCols();
  window.addEventListener("resize", updateGridCols);

  // Observe for DOM changes
  if (typeof MutationObserver !== "undefined") {
    new MutationObserver(updateGridCols).observe(document.body, {
      childList: true,
      subtree: true,
    });
  }
}

/**
 * Handle orientation changes
 */
function handleOrientationChange() {
  window.addEventListener("orientationchange", () => {
    setTimeout(() => {
      // Recalculate layouts after orientation change
      window.dispatchEvent(new Event("resize"));
    }, 100);
  });
}

/**
 * Detect viewport size changes and adjust layouts
 */
function initResizeObserver() {
  if (typeof ResizeObserver !== "undefined") {
    const resizeObserver = new ResizeObserver(() => {
      // Trigger custom resize event for responsive elements
      window.dispatchEvent(new CustomEvent("containerResize"));
    });

    document
      .querySelectorAll(".sidebar, .content-area, .right-panel")
      .forEach((el) => {
        resizeObserver.observe(el);
      });
  }
}

// Initialize responsive features when DOM is ready
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    initResponsiveHelper();
    handleOrientationChange();
    initAdaptiveGrid();
    initResizeObserver();
  });
} else {
  initResponsiveHelper();
  handleOrientationChange();
  initAdaptiveGrid();
  initResizeObserver();
}

// Export for external use
window.ResponsiveUtils = {
  isTouchDevice,
  getViewportHeight,
  updateBreakpoint: initResponsiveHelper,
};
