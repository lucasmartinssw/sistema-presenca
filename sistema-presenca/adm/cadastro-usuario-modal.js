// Script para modal de cadastro de usuário
function initializeCadastroUsuarioModal() {
    const btnCadastrar = document.getElementById('btnCadastrar');
    const form = document.getElementById('formCadastrarUsuario');
    const alertContainer = document.getElementById('alertContainer');
    const modal = document.getElementById('modalCadastrarUsuario');

    if (!btnCadastrar || !form || !alertContainer || !modal) {
        return; // Elementos não encontrados, sair da função
    }

    btnCadastrar.addEventListener('click', function() {
        const formData = new FormData(form);
        
        // Limpar alertas anteriores
        alertContainer.innerHTML = '';
        
        // Desabilitar botão durante o envio
        this.disabled = true;
        this.innerHTML = 'Cadastrando...';
        
        fetch('processar-cadastro-usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Mostrar alerta
            alertContainer.innerHTML = `
                <div class="alert alert-${data.type}" role="alert">
                    ${data.message}
                </div>
            `;
            
            if (data.success) {
                // Limpar formulário se sucesso
                form.reset();
                // Fechar modal após 2 segundos
                setTimeout(() => {
                    const bootstrapModal = bootstrap.Modal.getInstance(modal);
                    if (bootstrapModal) {
                        bootstrapModal.hide();
                    }
                    alertContainer.innerHTML = '';
                    
                    // Recarregar página se necessário (por exemplo, se cadastrou um professor na página de professores)
                    const currentPage = window.location.pathname;
                    const tipoUsuario = formData.get('tipoUsuario');
                    
                    if ((currentPage.includes('professores.php') && tipoUsuario === 'teacher') ||
                        (currentPage.includes('alunos.php') && tipoUsuario === 'student')) {
                        location.reload();
                    }
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alertContainer.innerHTML = `
                <div class="alert alert-danger" role="alert">
                    Erro de comunicação com o servidor.
                </div>
            `;
        })
        .finally(() => {
            // Reabilitar botão
            this.disabled = false;
            this.innerHTML = 'Cadastrar';
        });
    });

    // Limpar alertas ao fechar o modal
    modal.addEventListener('hidden.bs.modal', function () {
        alertContainer.innerHTML = '';
        form.reset();
    });
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    initializeCadastroUsuarioModal();
});
