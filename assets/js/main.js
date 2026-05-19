
const revealElements = document.querySelectorAll('.reveal');
const scrollObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
}, { threshold: 0.1 });

revealElements.forEach(el => scrollObserver.observe(el));


function toggleCart() {
    document.getElementById('cart-sidebar').classList.toggle('open');
    document.getElementById('overlay').classList.toggle('show');
}
function addToCart(id) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('id', id);

    fetch('cart_ajax.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        updateCartUI(data);
        showToast(" Шедевр добавлен в корзину!");
        document.getElementById('cart-sidebar').classList.add('open');
        document.getElementById('overlay').classList.add('show');
    });
}
function removeFromCart(id) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('id', id);

    fetch('cart_ajax.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        updateCartUI(data);
        showToast("Товар удален ");
    });
}

function updateCartUI(data) {
    document.getElementById('cart-items').innerHTML = data.html;
    document.getElementById('cart-total').innerText = data.total;
    const badge = document.getElementById('cart-badge');
    if(badge) badge.innerText = data.count;
}
function showToast(message) {
    const toast = document.getElementById('toast');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => { toast.classList.remove('show'); }, 3000);
}
function phoneMaskHandler(e) {
    let input = e.target;
    let value = input.value.replace(/\D/g, '');
    
    if (value.length > 0 && value[0] === '8') {
        value = '7' + value.substring(1);
    } else if (value.length > 0 && value[0] === '9') {
        value = '7' + value;
    }
    
    if (value.length > 11) value = value.slice(0, 11);
    
    let result = '';
    if (value.length > 0) {
        result = '+7';
        if (value.length > 1) result += '(' + value.slice(1, Math.min(4, value.length));
        if (value.length > 1 && value.length < 4) result += ')';
        if (value.length >= 4) result += ')-' + value.slice(4, Math.min(7, value.length));
        if (value.length >= 7) result += '-' + value.slice(7, Math.min(9, value.length));
        if (value.length >= 9) result += '-' + value.slice(9, 11);
    }
    
    input.value = result;
}