(() => {
'use strict';
const key='swiftorder-theme';
const root=document.documentElement;
const toggle=document.querySelector('[data-theme-toggle]');
const stored=()=>{try{const value=localStorage.getItem(key);return value==='dark'||value==='light'?value:null;}catch(_error){return null;}};
const preferred=()=>stored()|| (window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light');
const apply=(theme)=>{
root.dataset.theme=theme;
root.style.colorScheme=theme;
if(toggle instanceof HTMLButtonElement){const dark=theme==='dark';toggle.setAttribute('aria-pressed',String(dark));toggle.textContent=dark?'Light mode':'Dark mode';}
};
apply(preferred());
if(toggle instanceof HTMLButtonElement){toggle.addEventListener('click',()=>{const next=root.dataset.theme==='dark'?'light':'dark';apply(next);try{localStorage.setItem(key,next);}catch(_error){/* Current-page theme remains active. */}});}
})();
