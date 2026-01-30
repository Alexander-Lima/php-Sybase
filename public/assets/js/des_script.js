document.getElementById('uploadForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formFields = [
        "razaoSocial", 
        "inscricaoMunicipal",
        "codigoServico",
        "subItem",
        "regimeTributacao"
    ];

    const files = Array.from(document.getElementById('fileUpload').files)

    if (files.length === 0) {
        return alert('Por favor, selecione pelo menos um arquivo XML.');
    }

    const zippedFiles = await zipFiles(files);
    const form = prepareForm(formFields, zippedFiles);
    sendForm(form);
});

function prepareForm(formFields, zippedFiles) {
    const formData = new FormData();

    formFields.forEach(field => {
        const value = document.getElementById(field).value;
        formData.append(field, value);
    })

    formData.append('files', zippedFiles, 'xml.rar');

    return formData;
}

async function sendForm(formData) {
    toggleIcon(true);

    const response = await fetch('/php/des', {
      method: 'POST',
      body: formData
    });

    toggleIcon(false);

    if(response.ok) {
        downloadFile(response);

    } else {
        const body = await response.json();
        toggleError(true, body.message);
    }
}

function toggleIcon(visible) {
    toggleElement(visible, "noteIcon");
    toggleElement(!visible, "submitButton");
}

function toggleElement(visible, elementName) {
    const element = document.getElementById(elementName);
    
    element.style.display = visible ? "block" : "none";

    return element;
}

function toggleError(visible, message) {
    const box = toggleElement(visible, "errorBox");

    if(!visible) {
        return;
    }
    
    const text = document.getElementById("errorText");

    text.innerHTML = `Erro: ${message}`;
    box.scrollIntoView({ behavior: 'smooth' });

    setTimeout(() => toggleError(false), 5000);
}

async function downloadFile(response) {
    const blob = await response.blob();
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "DES.iss";
    document.body.appendChild(a);
    a.click();
    a.remove();

    URL.revokeObjectURL(url);
}

async function zipFiles(files) {
    const zip = new JSZip();
    
    files.forEach(async (file) => {
        const fileContent = file.arrayBuffer();
        zip.file(file.name, fileContent);
    });

    return await zip.generateAsync({ type: "blob" });
}