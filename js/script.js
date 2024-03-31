let btn = [];
let filter_btn = [];
let w = null;
let apply = null;
let div = null;
//apply = document.querySelector('.apply');

window.addEventListener('DOMContentLoaded', function() {
    btn = document.querySelectorAll('a.btn.btn-primary');
filter_btn = document.querySelectorAll('input.btn.btn-primary, input.btn.btn-outline-secondary');
w = [getComputedStyle(btn[0]).width, getComputedStyle(btn[1]).width ];
for (let i=0; i<btn.length; i++){
btn[i].addEventListener('mouseenter', function(event) {
	w_new = parseInt(w[i]) +  50;
	btn[i].style.width =w_new + 'px';
	btn[i].style.boxShadow = '0 0 1px 1px #000000';
});
btn[i].addEventListener('mouseout', function(event) {
	btn[i].style.width =w[i];
	btn[i].style.boxShadow = '';
});
}
for (let i=0; i<filter_btn.length; i++){
	
filter_btn[i].addEventListener('mousedown', function(event) {
	filter_btn[i].classList.add('filter');	
});
filter_btn[i].addEventListener('mouseup', function(event) {
	filter_btn[i].classList.remove('filter');
});
}
});



