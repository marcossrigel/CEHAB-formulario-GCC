<?php
declare(strict_types=1);

session_start();
header('Content-Type: text/html; charset=utf-8');

if (empty($_SESSION['responsavel'])) {
  http_response_code(401);
  exit("Acesso negado: sessão expirada. Abra novamente pelo link com token.");
}

$responsavel = $_SESSION['responsavel'];
?>

<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>CEHAB - Formulário GCC</title>

  <link rel="stylesheet" href="assets/css/forms.css">
</head>
<body>

  <main class="page">
    <div class="container">

      <header class="hero">
        <div class="hero__bar"></div>
        <div class="hero__content">
          <div class="hero__titlewrap">
            <h1 class="hero__title">CEHAB – Formulário GCC</h1>
            <p class="hero__subtitle">Planejamento / Levantamento interno – GCC</p>
            <p style="margin:10px 0 0; color:#333; font-weight:600;">
              Usuário: <?= htmlspecialchars($responsavel, ENT_QUOTES, 'UTF-8') ?>
            </p>
          </div>
          <p class="hero__required"><span>*</span> Pergunta obrigatória</p>
        </div>
      </header>

      <section class="formShell" aria-label="Questionário">
      <nav class="sectionNav" aria-label="Navegação por seções">
        <a class="sectionPill" data-step="1" href="secao_01.php">Seção 01</a>
        <a class="sectionPill" data-step="2" href="secao_02.php">Seção 02</a>
        <a class="sectionPill" data-step="3" href="secao_03.php">Seção 03</a>
        <a class="sectionPill" data-step="4" href="secao_04.php">Seção 04</a>
        <a class="sectionPill" data-step="5" href="secao_05.php">Seção 05</a>
      </nav>

      <article class="fieldCard" data-required="true" data-name="setor">
        <h3 class="fieldCard__title">
          1. Qual é o seu setor de atuação na CEHAB? <span class="req">*</span>
        </h3>

        <div class="choices" role="radiogroup" aria-label="Setor de atuação">
          <label class="choice">
            <input type="radio" name="setor" value="DP - Diretoria da Presidência">
            <span class="choice__text">DP (Diretoria da Presidência)</span>
          </label>
            <label class="choice">
              <input type="radio" name="setor" value="DAF - Diretoria de Administração e Finanças">
              <span class="choice__text">DAF (Diretoria de Administração e Finanças)</span>
            </label>
            <label class="choice">
              <input type="radio" name="setor" value="DSU - Diretoria de Obras de Saúde">
              <span class="choice__text">DSU (Diretoria de Obras de Saúde)</span>
            </label>
            <label class="choice">
              <input type="radio" name="setor" value="DED - Diretoria de Obras de Educação">
              <span class="choice__text">DED (Diretoria de Obras de Educação)</span>
            </label>
            <label class="choice">
              <input type="radio" name="setor" value="DIF - Diretoria de Infraestrutura">
              <span class="choice__text">DIF (Diretoria de Infraestrutura)</span>
            </label>
            <label class="choice">
              <input type="radio" name="setor" value="DSG - Diretoria de Obras de Segurança">
              <span class="choice__text">DSG (Diretoria de Obras de Segurança)</span>
            </label>
            <label class="choice">
              <input type="radio" name="setor" value="DOHDU - Diretoria de Obras de Habitação e Desenvolvimento Urbano">
              <span class="choice__text">DOHDU (Diretoria de Obras de Habitação e Desenvolvimento Urbano)</span>
            </label>
            <label class="choice">
              <input type="radio" name="setor" value="DPH - Diretoria de Programas Habitacionais">
              <span class="choice__text">DPH (Diretoria de Programas Habitacionais)</span>
            </label>
          </div>

          <div class="error">Selecione uma opção.</div>
      </article>

        <article class="fieldCard" data-required="true" data-name="tempo">
          <h3 class="fieldCard__title">
            2. Há quanto tempo você trabalha na CEHAB? <span class="req">*</span>
          </h3>

          <div class="choices" role="radiogroup" aria-label="Tempo na CEHAB">
            <label class="choice">
              <input type="radio" name="tempo" value="menos de 1 ano">
              <span class="choice__text">Menos de 1 ano</span>
            </label>
            <label class="choice">
              <input type="radio" name="tempo" value="entre 1 e 3 anos">
              <span class="choice__text">Entre 1 e 3 anos</span>
            </label>
            <label class="choice">
              <input type="radio" name="tempo" value="entre 3 e 6 anos">
              <span class="choice__text">Entre 3 e 6 anos</span>
            </label>
            <label class="choice">
              <input type="radio" name="tempo" value="mais de 6 anos">
              <span class="choice__text">Mais de 6 anos</span>
            </label>
          </div>

          <div class="error">Selecione uma opção.</div>
        </article>

        <div class="actions">
          <button class="btn" type="button" id="btnLimpar">Limpar</button>
          <button class="btn btn--primary" type="button" id="btnEnviar">Enviar</button>
        </div>

        <div class="toast" id="toastOk">
          ✅ Respostas registradas (exemplo). Veja o console para o JSON.
        </div>
      </section>

    </div>
  </main>

<script>
  const RESPONSAVEL = <?= json_encode($responsavel, JSON_UNESCAPED_UNICODE) ?>;
  const CURRENT_STEP = 4;
</script>

<script>
  const cards = Array.from(document.querySelectorAll(".fieldCard"));
  const toast = document.getElementById("toastOk");

  const SHEETS_WEBAPP_URL = "https://script.google.com/macros/s/AKfycbzDE0QAQAiFxjlm66qq40O37hNXzMe4RBQrEHXDwRXPM12tjvTfkp4NvpKJtmH0kf05/exec";


  function getRadioValue(name) {
    const checked = document.querySelector(`input[name="${name}"]:checked`);
    return checked ? checked.value : null;
  }

  function validate() {
    let ok = true;

    cards.forEach(card => {
      card.classList.remove("invalid");

      const required = card.dataset.required === "true";
      const name = card.dataset.name;

      if (required && !getRadioValue(name)) {
        ok = false;
        card.classList.add("invalid");
      }
    });

    if (!ok) {
      const first = document.querySelector(".fieldCard.invalid");
      if (first) first.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    return ok;
  }

  function collect() {
    return {
      responsavel: RESPONSAVEL,   // ✅ vem da sessão (PHP)
      setor: getRadioValue("setor"),
      tempo: getRadioValue("tempo")
    };
  }


  document.getElementById("btnEnviar").addEventListener("click", async () => {
  toast.style.display = "none";
  if (!validate()) return;

  const data = collect();

  try {
    const resp = await fetch(SHEETS_WEBAPP_URL, {
      method: "POST",
      headers: { "Content-Type": "text/plain;charset=utf-8" },
      body: JSON.stringify(data),
      redirect: "follow"
    });

    const text = await resp.text();
    const result = JSON.parse(text);

    if (!result.ok) throw new Error(result.error || "Falha ao gravar na planilha.");

    toast.textContent = "✅ Enviado para a planilha com sucesso!";
    toast.style.display = "block";
  } catch (err) {
    console.error(err);
    alert("Erro ao enviar: " + err.message);
  }
});

  document.getElementById("btnLimpar").addEventListener("click", () => {
    toast.style.display = "none";

    document.querySelectorAll("input[type='radio']").forEach(r => {
      r.checked = false;
    });

    cards.forEach(card => card.classList.remove("invalid"));
  });

  document.querySelectorAll("input[type='radio']").forEach(r => {
    r.addEventListener("change", () => {
      const card = r.closest(".fieldCard");
      if (card) card.classList.remove("invalid");
    });
  });


  // marca pill ativa
  document.querySelectorAll(".sectionPill").forEach(a => {
    const step = Number(a.dataset.step);
    if (step === CURRENT_STEP) a.classList.add("is-active");
  });

  // (opcional) aviso ao sair com alterações não enviadas
  let dirty = false;
  document.querySelectorAll("input[type='radio']").forEach(r => {
    r.addEventListener("change", () => dirty = true);
  });

  document.querySelectorAll(".sectionPill").forEach(a => {
    a.addEventListener("click", (e) => {
      if (!dirty) return;
      // se quiser deixar navegar sem aviso, remova este if
      const ok = confirm("Você alterou respostas e ainda não enviou. Quer sair mesmo assim?");
      if (!ok) e.preventDefault();
    });
  });
</script>


</body>
</html>
