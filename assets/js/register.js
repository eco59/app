// Variables booléennes
let pseudo = false;
let email = false;
let rgpd = false;
let pass = false;

// On charge les éléments du formulaire
document.querySelector("#registration_form_pseudo").addEventListener("input", checkPseudo);
document.querySelector("#registration_form_email").addEventListener("input", checkEmail);
document.querySelector("#registration_form_agreeTerms").addEventListener("input", checkRgpd);
document.querySelector("#registration_form_plainPassword").addEventListener("input", checkPass);

function checkPseudo(){
    pseudo = this.value.length > 2;
    checkAll();
}

function checkEmail(){
    let regex = new RegExp("\\S+@\\S+\\.\\S+");
    email = regex.test(this.value);
    checkAll();
}

function checkRgpd(){
    rgpd = this.checked;
    checkAll();
}

function checkPass() {
    // On récupère le mot de passe tapé
    let mdp = this.value;

    // Variables pour vérifier si le mot de passe contient au moins un chiffre, une majuscule, une minuscule et un caractère spécial
    let hasLower = /[a-z]/.test(mdp);
    let hasUpper = /[A-Z]/.test(mdp);
    let hasDigit = /[0-9]/.test(mdp);
    let hasSpecial = /[^A-Za-z0-9]/.test(mdp);

    // Récupérer l'élément pour afficher le statut du mot de passe
    let entropyElement = document.querySelector("#entropy");

    // Si toutes les conditions sont remplies, le mot de passe est valide
    if (hasLower && hasUpper && hasDigit && hasSpecial) {
        pass = true;
        entropyElement.textContent = "Mot de passe valide";
        entropyElement.classList.add("text-green");
        entropyElement.classList.remove("text-red");
    } else {
        pass = false;
        entropyElement.textContent = "Mot de passe invalide";
        entropyElement.classList.add("text-red");
        entropyElement.classList.remove("text-green");
    }

    // Appeler la fonction checkAll pour activer ou désactiver le bouton de soumission
    checkAll();
}

function checkAll(){
    document.querySelector("#submit-button").setAttribute("disabled", "disabled");
    if(email && pseudo && pass && rgpd){
        document.querySelector("#submit-button").removeAttribute("disabled");
    }
}
