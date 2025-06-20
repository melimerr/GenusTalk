class Auth {
  constructor(users) {
    this.users = users;
  }

  login(username, password) {
    return this.users.some(user => user.username === username && user.password === password);
  }
}

const users = [
  { username: 'Meli', password: '654'},
  { username: 'Ima', password: '123'},
  { username: 'Resi', password: '321'}
];

const auth = new Auth(users);

document.getElementById('loginForm').addEventListener('submit', function(event) {
  event.preventDefault();

  const usernameInput = document.getElementById('username').value;
  const passwordInput = document.getElementById('password').value;

  if (auth.login(usernameInput, passwordInput)) {
    window.location.href = 'dashboard.html';
  } else {
    alert('Incorrect username or password!');
    window.location.reload(); // Reloads the login page
  }
});