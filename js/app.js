import './bootstrap';


document.addEventListener("DOMContentLoaded", function () {

    const links = document.querySelectorAll(".menu-item");
    const sections = document.querySelectorAll(".section");

    function showSection(target) {
        sections.forEach(sec => sec.classList.add("hidden"));

        const el = document.querySelector(target);
        if (el) el.classList.remove("hidden");
    }

    links.forEach(link => {
        const target = link.getAttribute("href");

        // ✅ só adiciona comportamento se for âncora (#)
        if (target.startsWith("#")) {

            link.addEventListener("click", function (e) {
                e.preventDefault();

                showSection(target);

                links.forEach(l => l.classList.remove("active"));
                link.classList.add("active");

                window.location.hash = target;
            });

        }
    });

    // ✅ carregar direto
    if (window.location.hash) {
        showSection(window.location.hash);
    }

});

   <script>
document.addEventListener('DOMContentLoaded', () => {
  // DOM elements
  const dropdown = document.getElementById('dropdownCategoria');
  const toggleBtn = document.getElementById('categoriaToggle');
  const menu = document.getElementById('categoriaMenu');
  const checkboxes = Array.from(document.querySelectorAll('.categoria-checkbox'));
  const msg = document.getElementById('categoriaMaxMsg');
  const btnLimpar = document.getElementById('categoriaLimpar');
  const btnAplicar = document.getElementById('categoriaAplicar');
  const estagioSelect = document.getElementById('estagio_id');

  // Dados dos estágios vindo do PHP (array agrupado por id_categoria)
  const estagiosPorCategoria = @json($estagiosPorCategoria);

  // ----------------------------------------------------------------
  // FUNÇÕES AUXILIARES
  // ----------------------------------------------------------------

  // Retorna array com os IDs das categorias selecionadas
  function getSelectedIds() {
    return checkboxes.filter(cb => cb.checked).map(cb => parseInt(cb.value));
  }

  // Atualiza o texto do botão toggle
  function updateToggleText() {
    const selected = getSelectedIds();
    if (selected.length === 0) {
      toggleBtn.textContent = '{{ __("technology.select") }}';
    } else if (selected.length === 1) {
      const cb = checkboxes.find(c => c.checked);
      const nome = cb?.closest('.categoria-row')?.querySelector('.categoria-nome')?.textContent?.trim();
      toggleBtn.textContent = nome || selected[0];
    } else {
      toggleBtn.textContent = selected.length + ' categorias selecionadas';
    }
  }

  // Preenche o select de estágios de acordo com as categorias selecionadas
  function updateEstagios() {
    const selected = getSelectedIds();
    // Limpa o select
    estagioSelect.innerHTML = '<option value="">{{ __("technology.select_first_category") }}</option>';

  /*  // Caso 0 ou >2 → desabilita
    if (selected.length === 0 || selected.length > 2) {
      estagioSelect.disabled = true;
      return;
    }
*/
    let categoriaId;

    if (selected.length === 1) {
      // Caso 1 → estágios da categoria selecionada
      categoriaId = selected[0];
    } else { // selected.length === 2
      // Caso 2 → sempre usa a categoria 2 (Dispositivos e equipamentos em saúde)
      categoriaId = 2;
    }

    const estagios = estagiosPorCategoria[categoriaId.toString()] || [];
    if (estagios.length === 0) {
      const opt = document.createElement('option');
      opt.value = '';
      opt.textContent = 'Nenhum estágio disponível';
      opt.disabled = true;
      estagioSelect.appendChild(opt);
    } else {
      estagios.forEach(estagio => {
        const option = document.createElement('option');
        option.value = estagio.id;
        option.textContent = estagio.nome;
        // Marca como selecionado se houver valor antigo (old)
        if ({{ old('estagio_id') ?? 'null' }} == estagio.id) {
          option.selected = true;
        }
        estagioSelect.appendChild(option);
      });
    }
    estagioSelect.disabled = false;
  }

