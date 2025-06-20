document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const fullName = form.querySelector(
      'input[placeholder="Nama Lengkap"]'
    ).value;
    const email = form.querySelector('input[placeholder="Email"]').value;
    const username = form.querySelector('input[placeholder="Username"]').value;
    const password = form.querySelector('input[placeholder="Password"]').value;

    const user = {
      fullName,
      email,
      username,
      password,
    };

    // Simpan user ke localStorage
    localStorage.setItem("user", JSON.stringify(user));

    // alert("Pendaftaran berhasil! Silakan login.");  <-- hapus atau komentar
    window.location.href = "login.html"; // langsung diarahkan
  });
});
