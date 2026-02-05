// 3) Fazer um Promise verificando se o usuário é o ADM utilizando: Nome = ADM e Senha = ADM
const nome = "adm";
const senha = "adm";


var botaoLogar = document.querySelector('#logar');

botaoLogar.addEventListener('click', validar );





function validar(){

    const primeiraPromise = new Promise((resolve,reject) =>{
        let user = document.querySelector('#user').value;
        let senhaUser = document.querySelector('#senhaUser').value;
       
       
        if(user === nome & senhaUser === senha){
            resolve("User correto");
            window.alert("Seja bem vindo ao nosso site!");
            window.location.href = "http://localhost/moodle/pag-inicial.html";
        }else{
            reject("User incorreto! tente novamente!");
            window.alert("Usuário ou senha incorretos! Insira as informações novamente")
        }
    })
        
};