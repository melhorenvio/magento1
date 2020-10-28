function saveModel(event, url) {
    // console.log(event.target);
    url = url.replace('melhorenvio_model', event.target.value);
    Element.show('loading-mask');
    window.location.href=url
}

function tokenButton() {
    let token = document.getElementById("melhorenvio_general_token_melhorenvio").value
    let select_ambiente = document.getElementById("melhorenvio_general_ambiente")
    let environment = select_ambiente.options[select_ambiente.selectedIndex].value == "1" ? 'www' : 'sandbox'
    let settings = {
        "async": true,
        "crossDomain": true,
        "url": `https://${environment}.melhorenvio.com.br/api/v2/me`,
        "method": "GET",
        "headers": {
          "Authorization": "Bearer "+token,
          "Content-Type": "application/json",
          "Accept": "*/*",
        }
      }
      
      jQuery.ajax(settings).done(function (resp) {
          console.log(resp);
          console.log(settings);
          if(resp.email) {
            if(confirm("Deseja realmente buscar as informações do melhor envio e substituir?")) {
                let nome = document.getElementById("melhorenvio_empresa_razao_social").value = `${resp.firstname} ${resp.lastname}`;
                let cpf = document.getElementById("melhorenvio_empresa_cpf").value = resp.document;
                let cnpj = document.getElementById("melhorenvio_empresa_cnpj").value = (typeof resp.company_document == "undefined" ? "" : resp.company_document);
                let email = document.getElementById("melhorenvio_empresa_email").value = resp.email;
                let phone = document.getElementById("melhorenvio_empresa_telefone").value = resp.phone.phone;
                let cep = document.getElementById("melhorenvio_address_cep").value = resp.address.postal_code;
                let street = document.getElementById("melhorenvio_address_rua").value = resp.address.address;
                let numero = document.getElementById("melhorenvio_address_numero").value = resp.address.number;
                let complement = document.getElementById("melhorenvio_address_complem").value = resp.address.complement;
                let bairro = document.getElementById("melhorenvio_address_bairro").value = resp.address.district;
                let cidade = document.getElementById("melhorenvio_address_cidade").value = resp.address.city.city;
                let estado = document.getElementById("melhorenvio_address_estado").value = resp.address.city.state.state_abbr;
                alert("Finalizado.");
            }
        } else {
            alert("Valide se o token/ambiente está correto.");
        }
      });
}

jQuery(document).ready(() => {
    /**
     * Ajustando o system.xml
     * Inicio
     */
    const cpf_system_xml = document.getElementById('melhorenvio_empresa_cpf') || null;
    if (cpf_system_xml) { cpf_system_xml.maxLength = 18; }
    const cnpj_system_xml = document.getElementById('melhorenvio_empresa_cnpj') || null;
    if (cnpj_system_xml) { cnpj_system_xml.maxLength = 18; }
    const cep_system_xml = document.getElementById('melhorenvio_address_cep') || null;
    if(cep_system_xml) { cep_system_xml.maxLength = 9; }
    const street_system_xml = document.getElementById('melhorenvio_address_rua') || null;
    const district_system_xml = document.getElementById('melhorenvio_address_bairro') || null;
    const city_system_xml = document.getElementById('melhorenvio_address_cidade') || null;
    const state_system_xml = document.getElementById('melhorenvio_address_estado') || null;

    function cnpjListener() {
        this.value = this.value.split('.').join('');
        this.value = this.value.split('-').join('');
        this.value = this.value.split('/').join('');
        if(this.value.length == 14) {
            this.value = this.value
                .replace(/\D/g, '')
                .replace(/^(\d{2})(\d{3})?(\d{3})?(\d{4})?(\d{2})/, "$1.$2.$3/$4-$5");
        } else {
            this.value = this.value
                .replace(/\D/g, '')
                .replace(/^(\d{3})(\d{3})?(\d{3})?(\d{2})/, "$1.$2.$3-$4");
        }
    }

    function cepListener() {
        this.value = this.value
            .replace(/\D/g, '')
            .replace(/^(\d{5})(\d{3})/, "$1-$2");
        if(this.value.length == 9) {
            pesquisaCep(this.value);
        }
    }
        
    function pesquisaCep(valor) {
        //Nova variável "cep" somente com dígitos.
        let cep = valor.replace(/\D/g, '');
        
        let validacep = /^[0-9]{8}$/;

        //Valida o formato do CEP.
        if(validacep.test(cep)) {
            Element.show('loading-mask');

            //Preenche os campos com "..." enquanto consulta webservice.
            street_system_xml.value="...";
            district_system_xml.value="...";
            city_system_xml.value="...";
            state_system_xml.value="...";

            //Sincroniza com o callback.
            jQuery.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                if (!("erro" in dados)) {
                    //Atualiza os campos com os valores da consulta.
                    street_system_xml.value = dados.logradouro;
                    district_system_xml.value = dados.bairro;
                    city_system_xml.value = dados.localidade;
                    state_system_xml.value = dados.uf;
                } //end if.
                else {
                    //CEP pesquisado não foi encontrado.
                    alert("CEP não encontrado.");
                }

                Element.hide('loading-mask');
            });
        }
    };

    // events system.xml
    if(cnpj_system_xml) { cnpj_system_xml.addEventListener('focusout', cnpjListener); }
    if(cep_system_xml) { cep_system_xml.addEventListener('keyup', cepListener); }
    if(cep_system_xml) { cep_system_xml.addEventListener('change', cepListener); }
    /** FIM DO SYSTEM.XML */
});

// Atualiza o evento do botão "Salvar" para validar se o CNPJ ou o CPF está preenchido
window.addEventListener("load", () => {
    if(document.getElementById("melhorenvio_empresa_cnpj") != null && document.getElementById("melhorenvio_empresa_cpf") != null){
        function updateSaveButtonsEvents(){
            const saveButtons = document.getElementsByClassName("save");
    
            for(let saveButtonIndex in saveButtons){
                saveButtonIndex = parseInt(saveButtonIndex);
                
                const saveButton = saveButtons[saveButtonIndex];
        
                if(typeof saveButton != "undefined"){
                    const saveEvent = saveButton.onclick;
                    
                    saveButton.onclick = () => {
                        const melhorenvio_empresa_cnpj = document.getElementById("melhorenvio_empresa_cnpj");
                        const melhorenvio_empresa_cpf = document.getElementById("melhorenvio_empresa_cpf");
                        
                        const melhorenvio_address_rua = document.getElementById("melhorenvio_address_rua");
                        const melhorenvio_address_bairro = document.getElementById("melhorenvio_address_bairro");
                        const melhorenvio_address_cidade = document.getElementById("melhorenvio_address_cidade");
                        const melhorenvio_address_estado = document.getElementById("melhorenvio_address_estado");
                        
                        if(melhorenvio_empresa_cnpj.value.length == 0 && melhorenvio_empresa_cpf.value.length == 0){
                            alert("Informe o CNPJ ou o CPF para salvar as configurações");

                        }else if(melhorenvio_address_rua.value == "...") {
                            alert("O campo 'Endereço' não pode ser igual a '...', por favor altere e tente novamente");
                            melhorenvio_address_rua.focus();

                        }else if(melhorenvio_address_bairro.value == "...") {
                            alert("O campo 'Bairro' não pode ser igual a '...', por favor altere e tente novamente");
                            melhorenvio_address_bairro.focus();

                        }else if(melhorenvio_address_cidade.value == "...") {
                            alert("O campo 'Cidade' não pode ser igual a '...', por favor altere e tente novamente");
                            melhorenvio_address_cidade.focus();

                        }else if(melhorenvio_address_estado.value == "...") {
                            alert("O campo 'Estado / UF' não pode ser igual a '...', por favor altere e tente novamente");
                            melhorenvio_address_estado.focus();

                        }else{
                            saveEvent();
                        }
                    }
                }    
            }
        }
        
        updateSaveButtonsEvents();
    }
});