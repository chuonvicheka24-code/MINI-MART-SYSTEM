/* MINI MART — Reports page animations (count-up numbers + animated charts).
   Plays each time the admin opens the Reports panel (and when the vendor filter changes).
   Needs no changes to admin.js: it wraps showPanel() and the three chart drawers. */
(function(){
  "use strict";
  if(window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  if(typeof window.showPanel !== "function") return;

  let animating = false;                       // true only while a "play" render is running
  const EASE = "cubic-bezier(.2,.8,.2,1)";
  const SPRING = "cubic-bezier(.34,1.56,.64,1)";

  /* ---------- count-up numbers ---------- */
  function countUp(el, duration){
    const text = el.textContent.trim();
    const m = text.match(/^([^\d-]*)(-?[\d,]*\.?\d+)(.*)$/);
    if(!m) return;
    const prefix = m[1], suffix = m[3], raw = m[2].replace(/,/g, "");
    const target = parseFloat(raw);
    const decimals = (raw.split(".")[1] || "").length;
    const t0 = performance.now();
    el.classList.add("mm-counting");
    (function tick(now){
      const p = Math.min((now - t0) / duration, 1);
      const eased = 1 - Math.pow(1 - p, 3);                 // easeOutCubic
      el.textContent = prefix + (target * eased).toFixed(decimals) + suffix;
      if(p < 1) requestAnimationFrame(tick);
      else { el.textContent = text; el.classList.remove("mm-counting"); }
    })(t0);
  }
  function playNumbers(){
    ["report-total","report-orders","report-delivered","report-avg"].forEach((id, i) => {
      const el = document.getElementById(id);
      if(el) setTimeout(() => countUp(el, 1300), i * 120);
    });
  }

  /* ---------- helpers ---------- */
  const pose = (el, origin) => { el.style.transformBox = "fill-box"; el.style.transformOrigin = origin; };
  const svgOf = id => { const el = document.getElementById(id); return el ? el.querySelector("svg") : null; };
  const valueTexts = svg => svg.querySelectorAll('text[font-weight="700"]');

  /* ---------- line chart: line draws itself, area fades in, dots pop ---------- */
  function animateLine(id){
    const svg = svgOf(id); if(!svg) return;
    const line = svg.querySelector("polyline");
    const area = svg.querySelector("path");
    const dots = svg.querySelectorAll(".dotpt");
    const labels = valueTexts(svg);
    const DUR = 1400;

    if(line){
      const len = line.getTotalLength();
      line.style.strokeDasharray = len;
      const a = line.animate([{ strokeDashoffset: len }, { strokeDashoffset: 0 }],
        { duration: DUR, easing: "ease-in-out", fill: "backwards" });
      a.onfinish = () => { line.style.strokeDasharray = ""; };
    }
    if(area) area.animate([{ opacity: 0 }, { opacity: 1 }], { duration: 900, delay: DUR * .55, easing: "ease-out", fill: "backwards" });

    const n = Math.max(dots.length - 1, 1);
    dots.forEach((d, i) => {
      pose(d, "center");
      d.animate([{ transform: "scale(0)" }, { transform: "scale(1)" }],
        { duration: 450, delay: (DUR * i / n) * .9 + 150, easing: SPRING, fill: "backwards" });
    });
    labels.forEach((t, i) => {
      t.animate([{ opacity: 0, transform: "translateY(8px)" }, { opacity: 1, transform: "none" }],
        { duration: 450, delay: (DUR * i / n) * .9 + 300, easing: EASE, fill: "backwards" });
    });
  }

  /* ---------- bars: grow from the baseline, one after another ---------- */
  function animateBars(id, horizontal){
    const svg = svgOf(id); if(!svg) return;
    const bars = svg.querySelectorAll(".bar");
    const labels = valueTexts(svg);
    bars.forEach((b, i) => {
      const delay = Math.min(i * 70, 900);
      pose(b, horizontal ? "left center" : "center bottom");
      b.animate(
        [{ transform: horizontal ? "scaleX(0)" : "scaleY(0)" }, { transform: "scale(1)" }],
        { duration: 900, delay, easing: EASE, fill: "backwards" });
    });
    labels.forEach((t, i) => {
      t.animate([{ opacity: 0, transform: horizontal ? "translateX(-8px)" : "translateY(8px)" }, { opacity: 1, transform: "none" }],
        { duration: 500, delay: Math.min(i * 70, 900) + 450, easing: EASE, fill: "backwards" });
    });
  }

  /* ---------- wrap the three chart drawers (only animate during a "play") ---------- */
  function wrap(name, after){
    const orig = window[name];
    if(typeof orig !== "function") return;
    window[name] = function(){
      const r = orig.apply(this, arguments);
      if(animating) try{ after(arguments[0]); }catch(e){}
      return r;
    };
  }
  wrap("drawLineChart", id => animateLine(id));
  wrap("drawBarChart",  id => animateBars(id, false));
  wrap("drawHBarChart", id => animateBars(id, true));

  /* ---------- play when the Reports panel opens ---------- */
  const origShow = window.showPanel;
  window.showPanel = function(name){
    const isReports = name === "reports";
    animating = isReports;
    let r;
    try{ r = origShow.apply(this, arguments); } finally { animating = false; }
    if(isReports) playNumbers();
    return r;
  };

  /* ---------- re-grow purchase-order bars when the vendor filter changes ---------- */
  if(typeof window.setPOVendor === "function"){
    const origVendor = window.setPOVendor;
    window.setPOVendor = function(){
      animating = true;
      try{ return origVendor.apply(this, arguments); } finally { animating = false; }
    };
  }
})();
