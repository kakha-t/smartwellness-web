let lebensmittel = [];
let ausgewaehlt = [];

window.addEventListener("DOMContentLoaded", () => {
  ladeGruppen();
  ladeLebensmittel();
});

function ladeGruppen() {
  fetch("lade_gruppen.php")
    .then(res => res.json())
    .then(gruppen => {
      const select = document.getElementById("gruppe");
      gruppen.forEach(gr => {
        const opt = document.createElement("option");
        opt.value = gr.gruppe;
        opt.textContent = gr.gruppe;
        select.appendChild(opt);
      });
      select.addEventListener("change", ladeLebensmittel);
    });
}

function ladeLebensmittel() {
  const gruppe = document.getElementById("gruppe").value;
  let url = "lade_lebensmittel.php";
  if (gruppe) url += `?gruppe_id=${encodeURIComponent(gruppe)}`;

  fetch(url)
    .then(res => res.json())
    .then(daten => {
      lebensmittel = daten;
      zeigeLebensmittel();
    });
}

function zeigeLebensmittel() {
  const container = document.getElementById("produkt-liste");
  container.innerHTML = "";
  lebensmittel.forEach(lm => {
    const div = document.createElement("div");
    div.className = "produkt-item";
    div.innerHTML = `
      <label>
        <input type="checkbox" onchange="auswahlGeaendert(this)" 
               data-name="${lm.produkt}" data-kcal="${lm.kalorien}" 
               data-fett="${lm.fett}" data-eiweiss="${lm.eiweiss}" 
               data-kh="${lm.kohlenhydrate}" data-glyk="${lm.glyk_index}">
        ${lm.produkt} (${lm.kalorien} kcal/100g)
      </label>
      <input type="number" min="0" placeholder="Gramm" disabled>
    `;
    container.appendChild(div);
  });
}

function auswahlGeaendert(checkbox) {
  const grammInput = checkbox.parentElement.parentElement.querySelector("input[type='number']");
  grammInput.disabled = !checkbox.checked;
  grammInput.value = "";
  grammInput.addEventListener("input", berechneSummen);
  berechneSummen();
}

function berechneSummen() {
  ausgewaehlt = [];
  let kcal = 0, fett = 0, eiw = 0, kh = 0;

  document.querySelectorAll("#produkt-liste input[type='checkbox']:checked").forEach(cb => {
    const gramm = parseFloat(cb.parentElement.parentElement.querySelector("input[type='number']").value);
    if (!isNaN(gramm) && gramm > 0) {
      const eintrag = {
        produkt: cb.dataset.name,
        menge: gramm,
        kcal: +(cb.dataset.kcal * gramm / 100).toFixed(1),
        fett: +(cb.dataset.fett * gramm / 100).toFixed(1),
        eiweiss: +(cb.dataset.eiweiss * gramm / 100).toFixed(1),
        kh: +(cb.dataset.kh * gramm / 100).toFixed(1),
        glyk_index: cb.dataset.glyk || "?"
      };
      kcal += eintrag.kcal;
      fett += eintrag.fett;
      eiw += eintrag.eiweiss;
      kh += eintrag.kh;
      ausgewaehlt.push(eintrag);
    }
  });

  document.getElementById("summe-kcal").textContent = kcal.toFixed(1);
  document.getElementById("summe-fett").textContent = fett.toFixed(1);
  document.getElementById("summe-eiweiss").textContent = eiw.toFixed(1);
  document.getElementById("summe-kh").textContent = kh.toFixed(1);

  zeigeAuswahl();
}

function zeigeAuswahl() {
  const ausgabe = document.getElementById("auswahl");
  ausgabe.innerHTML = "";
  ausgewaehlt.forEach((eintrag, index) => {
    const div = document.createElement("div");
    div.className = "auswahl-eintrag";
    div.innerHTML = `${eintrag.produkt} - ${eintrag.menge}g (GI ${eintrag.glyk_index})
      <span class='entfernen-btn' onclick='entfernen(${index})'>✕</span>`;
    ausgabe.appendChild(div);
  });
}

function entfernen(index) {
  const name = ausgewaehlt[index].produkt;
  document.querySelectorAll(`#produkt-liste input[type='checkbox']`).forEach(cb => {
    if (cb.dataset.name === name) {
      cb.checked = false;
      cb.parentElement.parentElement.querySelector("input[type='number']").value = "";
      cb.parentElement.parentElement.querySelector("input[type='number']").disabled = true;
    }
  });
  berechneSummen();
}

function planSpeichern() {
  const tag = document.getElementById("tag").value.trim();
  const feedback = document.getElementById("save-feedback");

  if (!tag || ausgewaehlt.length === 0) {
    feedback.textContent = "❌ Bitte Tag angeben und Produkte auswählen.";
    feedback.className = "error-message";
    feedback.style.display = "block";
    return;
  }

  fetch("speichere_plan.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ tag, produkte: ausgewaehlt })
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
    feedback.textContent = data.message;
        feedback.className = "success-message";
      } else {
        feedback.textContent = "❌ Fehler beim Speichern.";
        feedback.className = "error-message";
      }
      feedback.style.display = "block";
    })
    .catch(() => {
      feedback.textContent = "❌ Netzwerkfehler beim Speichern.";
      feedback.className = "error-message";
      feedback.style.display = "block";
    });
}

document.addEventListener('DOMContentLoaded', () => {
  const produktListe = document.getElementById('produkt-liste');

  produktListe.addEventListener('input', () => {
    berechneSummen();
  });

  produktListe.addEventListener('change', (e) => {
    if (e.target.type === 'checkbox') {
      const itemDiv = e.target.closest('.produkt-item');
      if (itemDiv) {
        const grammFeld = itemDiv.querySelector('input[type="number"]');
        if (grammFeld) {
          if (e.target.checked) {
            grammFeld.classList.add('aktiv');
            grammFeld.disabled = false;
            grammFeld.focus();
          } else {
            grammFeld.classList.remove('aktiv');
            grammFeld.value = '';
            grammFeld.disabled = true;
          }
        }
      }
    }
  });
});