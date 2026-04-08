// Gestion du panier
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Mettre à jour le compteur du panier
function updateCartCount() {
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

// Ajouter au panier
function addToCart(id, name, price) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            quantity: 1
        });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    showNotification('Produit ajouté au panier!');
}

// Afficher le panier
function displayCart() {
    const cartContainer = document.getElementById('cart-items');
    const cartSummary = document.getElementById('cart-summary');
    
    if (!cartContainer) return;
    
    if (cart.length === 0) {
        cartContainer.innerHTML = '<p class="empty-cart">Votre panier est vide</p>';
        if (cartSummary) cartSummary.innerHTML = '';
        return;
    }
    
    let total = 0;
    
    cartContainer.innerHTML = `
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                ${cart.map(item => {
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    return `
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.price.toFixed(2)} €</td>
                            <td>
                                <input type="number" value="${item.quantity}" min="1" 
                                       onchange="updateQuantity(${item.id}, this.value)">
                            </td>
                            <td>${itemTotal.toFixed(2)} €</td>
                            <td>
                                <button onclick="removeFromCart(${item.id})" class="btn-remove">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('')}
            </tbody>
        </table>
    `;
    
    if (cartSummary) {
        cartSummary.innerHTML = `
            <div class="cart-total">
                <h3>Total: ${total.toFixed(2)} €</h3>
            </div>
        `;
    }
    
    // Ajouter le total à un champ caché pour le formulaire
    const totalInput = document.getElementById('total-hidden');
    if (totalInput) {
        totalInput.value = total;
    }
}

// Mettre à jour la quantité
function updateQuantity(id, quantity) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity = parseInt(quantity);
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            localStorage.setItem('cart', JSON.stringify(cart));
            displayCart();
            updateCartCount();
        }
    }
}

// Supprimer du panier
function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem('cart', JSON.stringify(cart));
    displayCart();
    updateCartCount();
    showNotification('Produit retiré du panier');
}

// Passer la commande
function checkout() {
    if (!isLoggedIn()) {
        showNotification('Veuillez vous connecter pour passer commande', 'error');
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
        return;
    }
    
    if (cart.length === 0) {
        showNotification('Votre panier est vide', 'error');
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    // Envoyer la commande au serveur
    fetch('api/checkout.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ cart: cart, total: total })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            localStorage.removeItem('cart');
            cart = [];
            updateCartCount();
            showNotification('Commande passée avec succès!');
            setTimeout(() => {
                window.location.href = 'mes-commandes.php';
            }, 2000);
        } else {
            showNotification(data.error || 'Erreur lors de la commande', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Erreur lors de la commande', 'error');
    });
}

// Vérifier si l'utilisateur est connecté
function isLoggedIn() {
    // Cette fonction doit être implémentée côté serveur
    // Pour l'instant, on vérifie un cookie ou une variable de session
    return document.body.getAttribute('data-logged-in') === 'true';
}

// Afficher les notifications
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Afficher les détails d'une commande
function showOrderDetails(orderId) {
    fetch(`api/get_order_details.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Afficher les détails dans une modale
                let modal = document.getElementById('order-modal');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'order-modal';
                    modal.className = 'modal';
                    document.body.appendChild(modal);
                }
                
                modal.innerHTML = `
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Détails de la commande #${orderId}</h3>
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.items.map(item => `
                                    <tr>
                                        <td>${item.product_name}</td>
                                        <td>${item.quantite}</td>
                                        <td>${parseFloat(item.prix).toFixed(2)} €</td>
                                        <td>${(item.quantite * item.prix).toFixed(2)} €</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3"><strong>Total</strong></td>
                                    <td><strong>${parseFloat(data.total).toFixed(2)} €</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;
                
                modal.style.display = 'block';
                
                const closeBtn = modal.querySelector('.close');
                closeBtn.onclick = () => {
                    modal.style.display = 'none';
                };
                
                window.onclick = (event) => {
                    if (event.target === modal) {
                        modal.style.display = 'none';
                    }
                };
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Erreur lors du chargement des détails', 'error');
        });
}

// Charger les produits avec filtres
function loadProducts(category = 'all') {
    const url = category === 'all' 
        ? 'api/get_products.php' 
        : `api/get_products.php?category=${encodeURIComponent(category)}`;
    
    fetch(url)
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('products-container');
            if (!container) return;
            
            container.innerHTML = products.map(product => `
                <div class="product-card" data-category="${product.categorie}">
                    <div class="product-image">
                        <i class="fas fa-capsules"></i>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${escapeHtml(product.nom)}</h3>
                        <p class="product-description">${escapeHtml(product.description.substring(0, 100))}...</p>
                        <div class="product-price">${parseFloat(product.prix).toFixed(2)} €</div>
                        <div class="product-stock">Stock: ${product.stock}</div>
                        ${product.ordonnance ? '<div class="prescription-badge">Sur ordonnance</div>' : ''}
                        <button class="btn-add-cart" onclick="addToCart(${product.id}, '${escapeHtml(product.nom)}', ${product.prix})">
                            Ajouter au panier
                        </button>
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading products:', error);
            showNotification('Erreur lors du chargement des produits', 'error');
        });
}

// Échapper les caractères HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    
    // Initialiser le panier sur la page panier
    if (document.getElementById('cart-items')) {
        displayCart();
        
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', checkout);
        }
    }
    
    // Configurer les filtres
    const filterBtns = document.querySelectorAll('.filter-btn');
    if (filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const category = btn.dataset.category;
                loadProducts(category);
            });
        });
    }
    
    // Charger les produits sur la page d'accueil
    if (document.getElementById('products-container')) {
        loadProducts();
    }
});