<!-- Modal Cadastrar Usuário -->
<div class="modal fade" id="modalCadastrarUsuario" tabindex="-1" aria-labelledby="modalCadastrarUsuarioLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCadastrarUsuarioLabel">
          <img src="../images/iflogov2.jpg" alt="Logo IF" width="30" height="30" class="me-2" />
          Cadastrar Novo Usuário
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="alertContainer"></div>
        <form id="formCadastrarUsuario">
          <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo</label>
            <input type="text" class="form-control" id="nome" name="nome" required />
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">E-mail institucional</label>
            <input type="email" class="form-control" id="email" name="email" required />
          </div>

          <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" id="senha" name="senha" required />
          </div>

          <div class="mb-3">
            <label for="confirmar" class="form-label">Confirmar Senha</label>
            <input type="password" class="form-control" id="confirmar" name="confirmar" required />
          </div>

          <div class="mb-3">
            <label class="form-label">Tipo de Usuário</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="tipoUsuario" id="admin" value="administrator" required />
              <label class="form-check-label" for="admin">Administrador</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="tipoUsuario" id="professor" value="teacher" required />
              <label class="form-check-label" for="professor">Professor</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="tipoUsuario" id="aluno" value="student" required />
              <label class="form-check-label" for="aluno">Aluno</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnCadastrar">Cadastrar</button>
      </div>
    </div>
  </div>
</div>
