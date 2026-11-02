/* MINI MART — click ripple + cart badge bump. Works on storefront and admin. */
(function(){
  "use strict";
  if(window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

  /* ---- click ripple on buttons ---- */
  const RIPPLE_SEL = ".btn, .add-btn, .deal-add, .flash-btn, .cat-scroll-btn, .qty-control button, .icon-btn, .btn-icon";

  document.addEventListener("pointerdown", function(e){
    const el = e.target.closest(RIPPLE_SEL);
    if(!el || el.disabled) return;

    const cs = getComputedStyle(el);
    if(cs.position === "static") el.style.position = "relative"; // never touch absolutely-positioned buttons
    el.style.overflow = "hidden";

    const rect = el.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const dot = document.createElement("span");
    dot.className = "mm-ripple";
    dot.style.width = dot.style.height = size + "px";
    dot.style.left = (e.clientX - rect.left - size / 2) + "px";
    dot.style.top  = (e.clientY - rect.top  - size / 2) + "px";
    el.appendChild(dot);
    dot.addEventListener("animationend", () => dot.remove());
  }, { passive: true });

  /* ---- bump the cart badge whenever its number changes ---- */
  function watchBadge(badge){
    if(badge._mmWatched) return;
    badge._mmWatched = true;
    new MutationObserver(() => {
      badge.classList.remove("mm-bump");
      void badge.offsetWidth;            // restart the animation
      badge.classList.add("mm-bump");
    }).observe(badge, { childList: true, characterData: true, subtree: true });
  }
  function init(){ document.querySelectorAll("[data-cart-count], .cart-count").forEach(watchBadge); }
  document.addEventListener("DOMContentLoaded", init);
  init();
})();
