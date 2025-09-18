// Get modal elements
const loginModal = document.getElementById("loginModal");
const registerModal = document.getElementById("registerModal");

// Get buttons that open modals
const openLogin = document.getElementById("openLogin");
const openRegister = document.getElementById("openRegister");

// Get <span> elements that close modals
const closeLogin = document.getElementById("closeLogin");
const closeRegister = document.getElementById("closeRegister");

// Open Login Modal
if(openLogin){
    openLogin.onclick = function(e){
        e.preventDefault();
        loginModal.style.display = "block";
    }
}

// Open Register Modal
if(openRegister){
    openRegister.onclick = function(e){
        e.preventDefault();
        registerModal.style.display = "block";
    }
}

// Close Login Modal
if(closeLogin){
    closeLogin.onclick = function(){
        loginModal.style.display = "none";
    }
}

// Close Register Modal
if(closeRegister){
    closeRegister.onclick = function(){
        registerModal.style.display = "none";
    }
}

// Close modal if click outside content
window.onclick = function(event){
    if(event.target == loginModal) loginModal.style.display = "none";
    if(event.target == registerModal) registerModal.style.display = "none";
}
