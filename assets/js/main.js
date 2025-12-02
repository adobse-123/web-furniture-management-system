// Main JS for the scaffold site
document.addEventListener('DOMContentLoaded', function(){
  // Log CTA clicks for now
  document.querySelectorAll('.btn').forEach(btn=>{
    btn.addEventListener('click', function(e){
      console.log('CTA clicked:', btn.textContent.trim());
    });
  });
});
