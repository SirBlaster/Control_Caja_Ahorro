
    const tipoCuenta = document.getElementById("tipoCuenta");
    const contenedorCampo = document.getElementById("contenedorCampo");
    const labelCampo = document.getElementById("labelCampo");
    const inputCampo = document.getElementById("inputCampo");
    const errorMessage = document.getElementById("mensajeError");

    tipoCuenta.addEventListener("change", () => {
        const tipo = tipoCuenta.value;

        // Mostrar campo dinámico
        contenedorCampo.style.display = "block";
        inputCampo.value = "";
        errorMessage.classList.add("d-none");

        if (tipo === "clabe") {
            labelCampo.textContent = "CLABE (18 dígitos)";
            inputCampo.maxLength = 18;
            inputCampo.placeholder = "Ingresa tu CLABE";
        } else if (tipo === "tarjeta") {
            labelCampo.textContent = "Número de tarjeta (16 dígitos)";
            inputCampo.maxLength = 16;
            inputCampo.placeholder = "Ingresa tu número de tarjeta";
        }
    });

    // Validación en tiempo real
    inputCampo.addEventListener("input", () => {
        const tipo = tipoCuenta.value;
        const valor = inputCampo.value;

        if (tipo === "clabe") {
            if (/^\d{18}$/.test(valor)) {
                errorMessage.classList.add("d-none");
            } else {
                errorMessage.textContent = "La CLABE debe contener 18 dígitos numéricos.";
                errorMessage.classList.remove("d-none");
            }
        }

        if (tipo === "tarjeta") {
            if (/^\d{16}$/.test(valor)) {
                errorMessage.classList.add("d-none");
            } else {
                errorMessage.textContent = "La tarjeta debe contener 16 dígitos numéricos.";
                errorMessage.classList.remove("d-none");
            }
        }
    });

