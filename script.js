// Initial site JS - small helper to demonstrate the scaffold
document.addEventListener('DOMContentLoaded',function(){
  const navLinks = document.querySelectorAll('.nav a');
  navLinks.forEach(a=>a.addEventListener('click',()=>{console.log('nav click',a.getAttribute('href'))}));
});
