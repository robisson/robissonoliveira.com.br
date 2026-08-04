(function () {
  const isEnglish = window.location.pathname.startsWith("/en/");
  const copy = isEnglish
    ? {
        eyebrow: "Simulator",
        title: "Tail latency, fan-out and hedging",
        play: "Play",
        pause: "Pause",
        intro:
          "Change the parameters to see how a small chance of an individual call becoming a straggler grows when a request fans out, and how hedging reduces the tail when another path has a real chance of responding quickly.",
        controlsLabel: "Simulation parameters",
        fanout: "Fan-out",
        callSingular: "call",
        callPlural: "calls",
        straggler: "Straggler chance",
        slowLatency: "Slow latency",
        hedgeAfter: "Hedge after",
        correlation: "Hedge correlation",
        risk: "At least one straggler",
        p99Base: "p99 without hedging",
        p99Hedge: "p99 with hedging",
        extraLoad: "Extra load",
        timelineCaption: "One request with fan-out",
        percentileCaption: "Simulated percentiles across 8,000 requests",
        note:
          "The simulation uses normal latency with small variation, occasional stragglers - calls that became much slower than the others - and a hedge attempt fired after the configured threshold. Correlation represents the chance that the alternate path suffers the same slowdown when the primary became slow.",
        timelineSummary: (base, hedged) => `simulated request: ${base} without hedge / ${hedged} with hedge`,
        hedgeMarker: "hedge",
        withoutMarker: "without",
        withMarker: "with",
        callLabel: (index) => `c${index + 1}`,
        showingCalls: (shown, total) => `showing ${shown} of ${total} calls`,
        withoutHedging: "without hedging",
        withHedging: "with hedging",
        locale: "en-US",
      }
    : {
        eyebrow: "Simulador",
        title: "Tail latency, fan-out e hedging",
        play: "Play",
        pause: "Pause",
        intro:
          "Altere os parâmetros para ver como uma pequena chance de uma chamada virar straggler cresce quando a requisição faz fan-out, e como o hedging reduz a cauda quando existe outro caminho com chance real de responder rápido.",
        controlsLabel: "Parâmetros da simulação",
        fanout: "Fan-out",
        callSingular: "chamada",
        callPlural: "chamadas",
        straggler: "Chance de straggler",
        slowLatency: "Latência lenta",
        hedgeAfter: "Hedge após",
        correlation: "Correlação do hedge",
        risk: "Pelo menos um straggler",
        p99Base: "p99 sem hedging",
        p99Hedge: "p99 com hedging",
        extraLoad: "Carga extra",
        timelineCaption: "Uma requisição com fan-out",
        percentileCaption: "Percentis simulados em 8.000 requisições",
        note:
          "A simulação usa latência normal com pequena variação, stragglers ocasionais - chamadas que ficaram muito mais lentas que as demais - e uma tentativa de hedge disparada após o limite configurado. A correlação representa a chance de o caminho alternativo sofrer a mesma lentidão quando a primária ficou lenta.",
        timelineSummary: (base, hedged) => `requisição simulada: ${base} sem hedge / ${hedged} com hedge`,
        hedgeMarker: "hedge",
        withoutMarker: "sem",
        withMarker: "com",
        callLabel: (index) => `c${index + 1}`,
        showingCalls: (shown, total) => `mostrando ${shown} de ${total} chamadas`,
        withoutHedging: "sem hedging",
        withHedging: "com hedging",
        locale: "pt-BR",
      };

  function mountSimulator() {
    const existing = document.querySelector("[data-tail-latency-simulator]");
    if (existing) return existing;

    const marker = Array.from(document.querySelectorAll(".article__body p")).find((paragraph) => {
      return paragraph.textContent.trim() === "[[tail-latency-simulator]]";
    });

    if (!marker) return null;

    const shell = document.createElement("section");
    shell.className = "tail-simulator";
    shell.setAttribute("data-tail-latency-simulator", "");
    shell.setAttribute("aria-labelledby", "tail-simulator-title");
    shell.innerHTML = `
      <header class="tail-simulator__header">
        <div>
          <p class="tail-simulator__eyebrow">${copy.eyebrow}</p>
          <h3 id="tail-simulator-title">${copy.title}</h3>
        </div>
        <button class="tail-simulator__button" type="button" data-action="play" aria-pressed="false">${copy.play}</button>
      </header>
      <p class="tail-simulator__intro">
        ${copy.intro}
      </p>
      <div class="tail-simulator__controls" aria-label="${copy.controlsLabel}">
        <label>
          <span>${copy.fanout}</span>
          <input type="range" min="1" max="200" step="1" value="100" data-control="fanout" />
          <output data-output="fanout">100 ${copy.callPlural}</output>
        </label>
        <label>
          <span>${copy.straggler}</span>
          <input type="range" min="0.1" max="10" step="0.1" value="1" data-control="straggler" />
          <output data-output="straggler">1,0%</output>
        </label>
        <label>
          <span>${copy.slowLatency}</span>
          <input type="range" min="80" max="1200" step="20" value="600" data-control="slow" />
          <output data-output="slow">600 ms</output>
        </label>
        <label>
          <span>${copy.hedgeAfter}</span>
          <input type="range" min="20" max="300" step="5" value="60" data-control="hedge" />
          <output data-output="hedge">60 ms</output>
        </label>
        <label>
          <span>${copy.correlation}</span>
          <input type="range" min="0" max="100" step="5" value="10" data-control="correlation" />
          <output data-output="correlation">10%</output>
        </label>
      </div>
      <div class="tail-simulator__metrics" aria-live="polite">
        <div><strong data-metric="risk">63,4%</strong><span>${copy.risk}</span></div>
        <div><strong data-metric="p99Base">-</strong><span>${copy.p99Base}</span></div>
        <div><strong data-metric="p99Hedge">-</strong><span>${copy.p99Hedge}</span></div>
        <div><strong data-metric="extraLoad">-</strong><span>${copy.extraLoad}</span></div>
      </div>
      <div class="tail-simulator__views">
        <figure class="tail-simulator__panel">
          <figcaption>${copy.timelineCaption}</figcaption>
          <canvas width="760" height="300" data-canvas="timeline"></canvas>
        </figure>
        <figure class="tail-simulator__panel">
          <figcaption>${copy.percentileCaption}</figcaption>
          <canvas width="760" height="300" data-canvas="percentiles"></canvas>
        </figure>
      </div>
      <p class="tail-simulator__note">
        ${copy.note}
      </p>
    `;
    marker.replaceWith(shell);
    return shell;
  }

  const root = mountSimulator();
  if (!root) return;

  const controls = {
    fanout: root.querySelector('[data-control="fanout"]'),
    straggler: root.querySelector('[data-control="straggler"]'),
    slow: root.querySelector('[data-control="slow"]'),
    hedge: root.querySelector('[data-control="hedge"]'),
    correlation: root.querySelector('[data-control="correlation"]'),
  };

  const outputs = {
    fanout: root.querySelector('[data-output="fanout"]'),
    straggler: root.querySelector('[data-output="straggler"]'),
    slow: root.querySelector('[data-output="slow"]'),
    hedge: root.querySelector('[data-output="hedge"]'),
    correlation: root.querySelector('[data-output="correlation"]'),
  };

  const metrics = {
    risk: root.querySelector('[data-metric="risk"]'),
    p99Base: root.querySelector('[data-metric="p99Base"]'),
    p99Hedge: root.querySelector('[data-metric="p99Hedge"]'),
    extraLoad: root.querySelector('[data-metric="extraLoad"]'),
  };

  const timelineCanvas = root.querySelector('[data-canvas="timeline"]');
  const percentileCanvas = root.querySelector('[data-canvas="percentiles"]');
  const playButton = root.querySelector('[data-action="play"]');
  let timer = null;

  function readParams() {
    return {
      fanout: Number(controls.fanout.value),
      stragglerRate: Number(controls.straggler.value) / 100,
      slowMs: Number(controls.slow.value),
      hedgeAfterMs: Number(controls.hedge.value),
      correlation: Number(controls.correlation.value) / 100,
      normalMinMs: 8,
      normalMaxMs: 22,
      sampleSize: 8000,
    };
  }

  function formatPercent(value) {
    return value.toLocaleString(copy.locale, {
      minimumFractionDigits: value < 10 ? 1 : 0,
      maximumFractionDigits: value < 10 ? 1 : 0,
    }) + "%";
  }

  function formatMs(value) {
    if (value >= 1000) {
      return (value / 1000).toLocaleString(copy.locale, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }) + " s";
    }
    return Math.round(value).toLocaleString(copy.locale) + " ms";
  }

  function randomBetween(min, max) {
    return min + Math.random() * (max - min);
  }

  function normalLatency(params) {
    return randomBetween(params.normalMinMs, params.normalMaxMs);
  }

  function slowLatency(params) {
    return params.slowMs * randomBetween(0.82, 1.18);
  }

  function primaryLatency(params) {
    return Math.random() < params.stragglerRate ? slowLatency(params) : normalLatency(params);
  }

  function backupLatency(params, primaryWasSlow) {
    if (primaryWasSlow && Math.random() < params.correlation) {
      return slowLatency(params);
    }
    return primaryLatency(params);
  }

  function percentile(sortedValues, p) {
    const index = Math.ceil((p / 100) * sortedValues.length) - 1;
    return sortedValues[Math.max(0, Math.min(index, sortedValues.length - 1))];
  }

  function simulateCall(params) {
    const primary = primaryLatency(params);
    const primaryWasSlow = primary > params.normalMaxMs;
    let withHedge = primary;
    let hedged = false;
    let backup = null;

    if (primary > params.hedgeAfterMs) {
      backup = backupLatency(params, primaryWasSlow);
      withHedge = Math.min(primary, params.hedgeAfterMs + backup);
      hedged = true;
    }

    return { primary, primaryWasSlow, backup, withHedge, hedged };
  }

  function simulateRequest(params, captureCalls) {
    const calls = [];
    let base = 0;
    let hedged = 0;
    let hedgeCount = 0;

    for (let i = 0; i < params.fanout; i += 1) {
      const call = simulateCall(params);
      base = Math.max(base, call.primary);
      hedged = Math.max(hedged, call.withHedge);
      if (call.hedged) hedgeCount += 1;
      if (captureCalls) calls.push(call);
    }

    return { base, hedged, hedgeCount, calls };
  }

  function simulateBatch(params) {
    const base = [];
    const hedged = [];
    let hedgeCount = 0;

    for (let i = 0; i < params.sampleSize; i += 1) {
      const result = simulateRequest(params, false);
      base.push(result.base);
      hedged.push(result.hedged);
      hedgeCount += result.hedgeCount;
    }

    base.sort((a, b) => a - b);
    hedged.sort((a, b) => a - b);

    return {
      base: {
        p50: percentile(base, 50),
        p95: percentile(base, 95),
        p99: percentile(base, 99),
      },
      hedged: {
        p50: percentile(hedged, 50),
        p95: percentile(hedged, 95),
        p99: percentile(hedged, 99),
      },
      extraLoad: hedgeCount / (params.sampleSize * params.fanout),
    };
  }

  function setupCanvas(canvas) {
    const ctx = canvas.getContext("2d");
    const ratio = window.devicePixelRatio || 1;
    const cssWidth = canvas.clientWidth || canvas.width;
    const cssHeight = Math.round(cssWidth * (canvas.height / canvas.width));
    canvas.width = Math.round(cssWidth * ratio);
    canvas.height = Math.round(cssHeight * ratio);
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    return { ctx, width: cssWidth, height: cssHeight };
  }

  function drawText(ctx, text, x, y, options) {
    ctx.fillStyle = options.color || "#252d35";
    ctx.font = options.font || "12px Helvetica, Arial, sans-serif";
    ctx.textAlign = options.align || "left";
    ctx.textBaseline = options.baseline || "alphabetic";
    ctx.fillText(text, x, y);
  }

  function drawTimeline(params) {
    const { ctx, width, height } = setupCanvas(timelineCanvas);
    const result = simulateRequest(params, true);
    const calls = result.calls.slice(0, Math.min(params.fanout, 34));
    const left = 88;
    const right = 18;
    const top = 34;
    const rowHeight = Math.max(5, Math.min(10, (height - 70) / Math.max(calls.length, 1)));
    const maxLatency = Math.max(params.slowMs * 1.25, result.base, result.hedged, params.hedgeAfterMs + params.slowMs);
    const scale = (width - left - right) / maxLatency;

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, width, height);

    drawText(ctx, copy.timelineSummary(formatMs(result.base), formatMs(result.hedged)), 14, 18, {
      color: "#252d35",
      font: "12px Helvetica, Arial, sans-serif",
    });

    ctx.strokeStyle = "#d9dee2";
    ctx.lineWidth = 1;
    [params.hedgeAfterMs, result.base, result.hedged].forEach((value) => {
      const x = left + value * scale;
      ctx.beginPath();
      ctx.moveTo(x, top - 8);
      ctx.lineTo(x, height - 24);
      ctx.stroke();
    });

    drawText(ctx, copy.hedgeMarker, left + params.hedgeAfterMs * scale, height - 8, { color: "#747d86", align: "center" });
    drawText(ctx, copy.withoutMarker, left + result.base * scale, height - 8, { color: "#f69087", align: "center" });
    drawText(ctx, copy.withMarker, left + result.hedged * scale, height - 8, { color: "#85a9b3", align: "center" });

    calls.forEach((call, index) => {
      const y = top + index * rowHeight;
      const primaryWidth = Math.max(2, call.primary * scale);
      ctx.fillStyle = call.primaryWasSlow ? "#f69087" : "#b0cb7a";
      ctx.fillRect(left, y, primaryWidth, Math.max(3, rowHeight - 2));

      if (call.hedged && call.backup !== null) {
        const hedgeX = left + params.hedgeAfterMs * scale;
        const backupWidth = Math.max(2, call.backup * scale);
        ctx.fillStyle = "#85a9b3";
        ctx.fillRect(hedgeX, y + Math.max(1, rowHeight / 2), backupWidth, Math.max(2, rowHeight / 2 - 1));
      }

      if (index < 8 || index === calls.length - 1) {
        drawText(ctx, copy.callLabel(index), 14, y + rowHeight - 2, { color: "#747d86", font: "10px Helvetica, Arial, sans-serif" });
      }
    });

    if (params.fanout > calls.length) {
      drawText(ctx, copy.showingCalls(calls.length, params.fanout), 14, height - 8, {
        color: "#747d86",
        font: "11px Helvetica, Arial, sans-serif",
      });
    }
  }

  function drawPercentiles(stats) {
    const { ctx, width, height } = setupCanvas(percentileCanvas);
    const left = 50;
    const right = 18;
    const top = 28;
    const bottom = 42;
    const chartHeight = height - top - bottom;
    const chartWidth = width - left - right;
    const groups = [
      { label: "p50", base: stats.base.p50, hedged: stats.hedged.p50 },
      { label: "p95", base: stats.base.p95, hedged: stats.hedged.p95 },
      { label: "p99", base: stats.base.p99, hedged: stats.hedged.p99 },
    ];
    const maxValue = Math.max(...groups.flatMap((group) => [group.base, group.hedged]), 100);
    const groupWidth = chartWidth / groups.length;
    const barWidth = Math.min(42, groupWidth * 0.25);

    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, width, height);

    ctx.strokeStyle = "#d9dee2";
    ctx.lineWidth = 1;
    for (let i = 0; i <= 4; i += 1) {
      const y = top + chartHeight * (i / 4);
      ctx.beginPath();
      ctx.moveTo(left, y);
      ctx.lineTo(width - right, y);
      ctx.stroke();
      const value = maxValue * (1 - i / 4);
      drawText(ctx, formatMs(value), left - 8, y + 4, {
        color: "#747d86",
        font: "10px Helvetica, Arial, sans-serif",
        align: "right",
      });
    }

    groups.forEach((group, index) => {
      const center = left + groupWidth * index + groupWidth / 2;
      const baseHeight = (group.base / maxValue) * chartHeight;
      const hedgeHeight = (group.hedged / maxValue) * chartHeight;
      const baseX = center - barWidth - 4;
      const hedgeX = center + 4;
      const baseY = top + chartHeight - baseHeight;
      const hedgeY = top + chartHeight - hedgeHeight;

      ctx.fillStyle = "#f69087";
      ctx.fillRect(baseX, baseY, barWidth, baseHeight);
      ctx.fillStyle = "#85a9b3";
      ctx.fillRect(hedgeX, hedgeY, barWidth, hedgeHeight);

      drawText(ctx, group.label, center, height - 18, { color: "#252d35", align: "center" });
      drawText(ctx, formatMs(group.base), baseX + barWidth / 2, Math.max(14, baseY - 6), {
        color: "#f69087",
        font: "10px Helvetica, Arial, sans-serif",
        align: "center",
      });
      drawText(ctx, formatMs(group.hedged), hedgeX + barWidth / 2, Math.max(14, hedgeY - 6), {
        color: "#85a9b3",
        font: "10px Helvetica, Arial, sans-serif",
        align: "center",
      });
    });

    ctx.fillStyle = "#f69087";
    ctx.fillRect(left, height - 10, 10, 3);
    drawText(ctx, copy.withoutHedging, left + 14, height - 7, { color: "#747d86", font: "10px Helvetica, Arial, sans-serif" });
    ctx.fillStyle = "#85a9b3";
    ctx.fillRect(left + 96, height - 10, 10, 3);
    drawText(ctx, copy.withHedging, left + 110, height - 7, { color: "#747d86", font: "10px Helvetica, Arial, sans-serif" });
  }

  function render() {
    const params = readParams();
    const stats = simulateBatch(params);
    const risk = 1 - Math.pow(1 - params.stragglerRate, params.fanout);

    outputs.fanout.textContent = `${params.fanout} ${params.fanout === 1 ? copy.callSingular : copy.callPlural}`;
    outputs.straggler.textContent = formatPercent(params.stragglerRate * 100);
    outputs.slow.textContent = formatMs(params.slowMs);
    outputs.hedge.textContent = formatMs(params.hedgeAfterMs);
    outputs.correlation.textContent = formatPercent(params.correlation * 100);

    metrics.risk.textContent = formatPercent(risk * 100);
    metrics.p99Base.textContent = formatMs(stats.base.p99);
    metrics.p99Hedge.textContent = formatMs(stats.hedged.p99);
    metrics.extraLoad.textContent = formatPercent(stats.extraLoad * 100);

    drawTimeline(params);
    drawPercentiles(stats);
  }

  function stop() {
    if (timer) window.clearInterval(timer);
    timer = null;
    playButton.textContent = copy.play;
    playButton.setAttribute("aria-pressed", "false");
  }

  Object.values(controls).forEach((control) => {
    control.addEventListener("input", () => {
      stop();
      render();
    });
  });

  playButton.addEventListener("click", () => {
    if (timer) {
      stop();
      return;
    }
    playButton.textContent = copy.pause;
    playButton.setAttribute("aria-pressed", "true");
    timer = window.setInterval(render, 900);
    render();
  });

  window.addEventListener("resize", render);
  render();
})();
