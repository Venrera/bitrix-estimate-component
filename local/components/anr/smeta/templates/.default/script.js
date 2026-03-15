
//available fields data-smeta-cat, data-col

document.addEventListener('DOMContentLoaded', function () {
 const form = document.getElementById('smetaform');
 const requiredInputs = form.querySelectorAll('[required]');
	
 requiredInputs.forEach(input => {
    input.addEventListener('change', function () {
		const ok = checkRequiredInput(input);
		if (ok && input.type === 'file') {checkFileInput(input);}
    });
  });
  
  form.addEventListener('submit', function () {

  const submitBtn = form.querySelector('[type="submit"]');

  if (submitBtn) {
    submitBtn.disabled = true;
	submitBtn.value = "Saving...";
  }

   setTimeout(() => {
    if (submitBtn) {
      submitBtn.disabled = false;
      }
    }, 5000);
  });
  
document.addEventListener('click', function (e) {
  if (e.target.classList.contains('form-add-button')) {
  e.preventDefault();
    const btn = e.target;
		
    const catIndex = btn.dataset.smetaCat; 
    const table = document.querySelector(`.b-applications-smeta[data-smeta-cat="${catIndex}"]`);
    if (!table) return;
    const tbody = table.querySelector('.smeta-body');
    const rows = tbody.querySelectorAll('.smeta-row');
    const newRowIndex = rows.length;
    let name, id;
		
    const tr = document.createElement('tr');
	  tr.className = 'smeta-row';
		
    for (let k = 0; k < Number(table.dataset.col); k++) {
	    const td = document.createElement('td');
	    if (k === 0) {
		    name = `cat[${catIndex}][${newRowIndex}]`;
		    id   = `cat${catIndex}_${newRowIndex}`;
		    td.className = 'application_th';
		    td.innerHTML = `<input type="text" name="${name}" id="${id}">`;
      } else {
		    name = `pay[${catIndex}][${newRowIndex}][${k}]`;
		    id   = `pay${catIndex}_${newRowIndex}_${k}`;
		    td.innerHTML = `<input type="number" name="${name}" id="${id}">`;
      }
	    tr.appendChild(td);
    }
    const addButtonRow = btn.closest('tr');
    tbody.insertBefore(tr, addButtonRow);
  }
 
  if (e.target.classList.contains('form-del-button')) {
    e.preventDefault();

    const btn = e.target;
    const catIndex = btn.dataset.smetaCat;
    const table = document.querySelector(`.b-applications-smeta[data-smeta-cat="${catIndex}"]`);
    if (!table) return;
    const tbody = table.querySelector('.smeta-body');
    const rows = tbody.querySelectorAll('.smeta-row');

   // 🚫 keep the first string
    if (rows.length <= 1) {
      return;
    }
    rows[rows.length - 1].remove();
  }
});

document.addEventListener('input', function(e){

  if (!e.target.closest('.smeta-row')) return;

  const row = e.target.closest('.smeta-row');
  const table = row.closest('.b-applications-smeta');
  const colCount = Number(table.dataset.col);
  const inputs = row.querySelectorAll('input[type="number"]');

  let result = 1;

  for (let i = 0; i < colCount - 2; i++) {
    const val = parseFloat(inputs[i].value) || 0;
    result *= val;
  }
  const sumInput = inputs[colCount - 2];
  sumInput.value = result ? Math.round(result) : "";
  
  recalcCategory(table, colCount);
});

function recalcCategory(table, colCount){
  const rows = table.querySelectorAll('.smeta-row');

  let total = 0;

  rows.forEach(row => {
    const inputs = row.querySelectorAll('input[type="number"]');
    const val = parseInt(inputs[colCount - 2].value) || 0;
    total += val;
  });

 const catIndex = table.dataset.smetaCat;
 const itogInput = document.getElementById(`itog${catIndex}`);
 console.log("itogId", `itog${catIndex}`);

  if (itogInput){
    itogInput.value = total ? Math.round(total) : "";
  }
}
});
