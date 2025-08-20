// script.js
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('formMensaje');
  if (!form) return;
  form.addEventListener('submit', function(e){
    const nombre = document.getElementById('nombre').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();
    if (!nombre || !mensaje) {
      e.preventDefault();
      alert('Por favor completa nombre y mensaje.');
    }
  });
});
