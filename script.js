async function sendToAPI(pergunta) {
  statusText.textContent = "Processando...";

  try {
    const response = await fetch('/api/pergunta', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ texto: pergunta })
    });

    const data = await response.json();
    output.textContent = data.resposta;

    // Fala a resposta
    const synth = window.speechSynthesis;
    const utterThis = new SpeechSynthesisUtterance(data.resposta);
    utterThis.lang = "pt-BR";
    utterThis.rate = speechRate;
    synth.speak(utterThis);

    statusText.textContent = "Clique para falar novamente";
  } catch (error) {
    output.textContent = "Erro ao comunicar com o servidor.";
    statusText.textContent = "Erro";
  }
}
