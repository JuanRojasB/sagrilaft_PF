function initMapLocationPicker(addressInputId) {
    const d = document.getElementById(addressInputId);
    if (!d) return;

    // Find the parent .fv cell — inject widget INSIDE it, not before the input
    const fv = d.closest('.fv') || d.parentNode;

    // Convert to hidden input so it stays in FormData but is invisible
    d.type = 'hidden';
    d.removeAttribute('required');

    const c = document.createElement('div');
    c.style.cssText = 'display:flex;flex-direction:column;gap:8px;width:100%';
    fv.insertBefore(c, d);

c.innerHTML = `<div style="display:grid;grid-template-columns:90px 55px 65px 55px 55px 1fr;gap:5px;align-items:end">
<div><label style="display:block;color:var(--text-secondary);font-size:12px;margin-bottom:3px;font-weight:600">Tipo</label>
<select id="v-${addressInputId}" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')" style="width:100%;padding:6px 3px;background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-primary);border-radius:4px;font-size:13px">
<option value="">-</option><option value="Calle">Calle</option><option value="Carrera">Carrera</option>
<option value="Avenida">Avenida</option><option value="Diagonal">Diagonal</option><option value="Transversal">Transversal</option>
</select></div>
<div><label style="display:block;color:var(--text-secondary);font-size:12px;margin-bottom:3px;font-weight:600">Nº</label>
<input type="text" id="n-${addressInputId}" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')" maxlength="4" style="width:100%;padding:6px;background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-primary);border-radius:4px;font-size:13px;text-align:center"></div>
<div><label style="display:block;color:var(--text-secondary);font-size:12px;margin-bottom:3px;font-weight:600">Orient.</label>
<select id="o-${addressInputId}" style="width:100%;padding:6px 2px;background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-primary);border-radius:4px;font-size:12px">
<option value=""></option><option value="Sur">Sur</option><option value="Norte">Norte</option><option value="Este">Este</option><option value="Oeste">Oeste</option>
</select></div>
<div><label style="display:block;color:var(--text-secondary);font-size:12px;margin-bottom:3px;font-weight:600">#</label>
<input type="text" id="n1-${addressInputId}" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')" maxlength="4" style="width:100%;padding:6px;background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-primary);border-radius:4px;font-size:13px;text-align:center"></div>
<div><label style="display:block;color:var(--text-secondary);font-size:12px;margin-bottom:3px;font-weight:600">-</label>
<input type="text" id="n2-${addressInputId}" required oninvalid="this.setCustomValidity('Requerido')" oninput="this.setCustomValidity('')" maxlength="4" style="width:100%;padding:6px;background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-primary);border-radius:4px;font-size:13px;text-align:center"></div>
<div><label style="display:block;color:var(--text-secondary);font-size:12px;margin-bottom:3px;font-weight:600">Complemento</label>
<input type="text" id="c-${addressInputId}" maxlength="30" style="width:100%;padding:6px 7px;background:var(--bg-input);color:var(--text-primary);border:1px solid var(--border-primary);border-radius:4px;font-size:13px"></div>
</div>
<div style="background:linear-gradient(135deg,rgba(59,130,246,0.08),rgba(37,99,235,0.03));padding:6px 10px;border-radius:4px;border-left:3px solid var(--accent-primary)">
<div style="color:var(--text-muted);font-size:10px;margin-bottom:2px;text-transform:uppercase;letter-spacing:0.5px">VISTA PREVIA:</div>
<div id="p-${addressInputId}" style="color:var(--text-primary);font-size:13px;font-weight:600;min-height:18px"></div>
</div>
<div id="err-${addressInputId}" style="display:none;background:rgba(239,68,68,0.1);border-left:3px solid #ef4444;padding:6px 10px;border-radius:4px;margin-top:4px">
<div style="color:#fca5a5;font-size:12px;font-weight:600">⚠️ Complete los campos obligatorios de la dirección (Tipo, Nº, #, -)</div>
</div>`;

    const v   = document.getElementById(`v-${addressInputId}`);
    const n   = document.getElementById(`n-${addressInputId}`);
    const o   = document.getElementById(`o-${addressInputId}`);
    const n1  = document.getElementById(`n1-${addressInputId}`);
    const n2  = document.getElementById(`n2-${addressInputId}`);
    const co  = document.getElementById(`c-${addressInputId}`);
    const p   = document.getElementById(`p-${addressInputId}`);
    const err = document.getElementById(`err-${addressInputId}`);

    function u() {
        const incomplete = !v.value || !n.value.trim() || !n1.value.trim() || !n2.value.trim();
        if (incomplete) {
            p.textContent = '';
            d.value = '';
            err.style.display = (v.value || n.value.trim() || n1.value.trim() || n2.value.trim()) ? 'block' : 'none';
            return;
        }
        err.style.display = 'none';
        let a = v.value + ' ' + n.value.trim();
        if (o.value) a += ' ' + o.value;
        a += ' #' + n1.value.trim() + '-' + n2.value.trim();
        if (co.value.trim()) a += ' ' + co.value.trim();
        p.textContent = a;
        d.value = a;
    }

    [v, n, o, n1, n2, co].forEach(f => {
        f.addEventListener('input', u);
        f.addEventListener('change', u);
    });
    u();
}
