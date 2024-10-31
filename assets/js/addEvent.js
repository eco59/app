document.querySelector("#ajout_event_form_Image").addEventListener("change", checkFile);
function checkFile(){
    let preview = document.querySelector(".preview");
    let image = preview.querySelector("img");
    let file = this.files[0];
    const types = ["image/jpeg", "image/png", "image/webp"];
    let reader = new FileReader();

    reader.onloadend = function(){
        image.src = reader.result;
        preview.style.display ="block";
    }


    //on verifie qu'un fichier existe
    if(file){
        //on verifie que le fichier est bien une image accepter
        if(types.includes(file.type)){
            reader.readAsDataURL(file);
        }
    }else{
        image.src= "";
        preview.style.display = "none"
    }
}