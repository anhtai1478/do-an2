document.addEventListener("click", async function (event) {
    const buttonaddToCart = event.target.closest(".add-to-cart");

    if (!buttonaddToCart) {
        return;
    }
    const productId = buttonaddToCart.dataset.productId;
    const quantityInput = buttonaddToCart
        .closest(".product-information")
        ?.querySelector("#product-quantity");
    const quantity = quantityInput ? Number(quantityInput.value) : 1;
    const url = document
        .querySelector('meta[name="cart-add-url"]')
        .content.replace("__ID__", productId);

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ quantity: quantity }),
        });
        if (response.status === 401) {
            alert("Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!");
            window.location.href = "/login";
            return;
        }
        if (!response.ok) {
            throw new Error("không thể thêm sản phẩm vào giỏ hàng");
        }
        const result = await response.json();
        document.getElementById("cart-count").textContent = result.count;
    } catch (error) {
        console.error("Error:", error);
        alert(
            "Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng. Vui lòng thử lại.",
        );
    }
});

document.addEventListener("click", async function (event) {
    const actionBtn = event.target.closest(
        ".cart_quantity_up,.cart_quantity_down,.cart_quantity_delete",
    );

    if (!actionBtn) {
        return;
    }

    event.preventDefault();

    const cartSection = document.getElementById("cart_items");
    if (!cartSection) {
        return;
    }

    const url = cartSection.dataset.updateUrl;
    const productId = actionBtn.dataset.id;
    const action = actionBtn.dataset.action;

    try {
        const response = await fetch(url, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ id: productId, action: action }),
        });
        if (!response.ok) {
            throw new Error("Không thể truy cập giỏ hàng");
        }

        const result = await response.json();

        // 1. Cập nhật số lượng + thành tiền của dòng đó, hoặc xóa dòng nếu hết / bấm X
        const row = document.getElementById('cart-row-' + productId);
        if (result.item_exists && row) {
            row.querySelector('.cart_quantity_input').value = result.item_quantity;
            document.getElementById('item-total-' + productId).textContent = '$' + Number(result.item_total).toFixed(2);
        } else if (row) {
            row.remove();
        }
        // 2. Nếu xóa hết sạch sản phẩm thì hiện dòng thông báo "Giỏ hàng trống"
        const tbody = document.getElementById('cart-body');
        if (tbody && tbody.querySelectorAll('tr[data-id]').length === 0) {
            tbody.innerHTML = '<tr id="cart-empty-row"><td colspan="6" class="text-center" style="padding: 30px;">Giỏ hàng của bạn đang trống.</td></tr>';
        }
        // 3. Cập nhật số trên Header và Tổng tiền tất cả sản phẩm bên dưới
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = result.cart_count;
        }
        document.getElementById('cart-sub-total').textContent = '$' + Number(result.sub_total).toFixed(2);
        document.getElementById('cart-eco-tax').textContent = '$' + Number(result.eco_tax).toFixed(2);
        document.getElementById('cart-grand-total').textContent = '$' + Number(result.grand_total).toFixed(2);
    } catch (error) {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi cập nhật giỏ hàng. Vui lòng thử lại.');
    }
});

    


/*price range*/

if ($.fn.slider) {
    $("#sl2").slider();
}

var RGBChange = function () {
    $("#RGB").css(
        "background",
        "rgb(" + r.getValue() + "," + g.getValue() + "," + b.getValue() + ")",
    );
};

/*scroll to top*/

$(document).ready(function () {
    $(function () {
        $.scrollUp({
            scrollName: "scrollUp", // Element ID
            scrollDistance: 300, // Distance from top/bottom before showing element (px)
            scrollFrom: "top", // 'top' or 'bottom'
            scrollSpeed: 300, // Speed back to top (ms)
            easingType: "linear", // Scroll to top easing (see http://easings.net/)
            animation: "fade", // Fade, slide, none
            animationSpeed: 200, // Animation in speed (ms)
            scrollTrigger: false, // Set a custom triggering element. Can be an HTML string or jQuery object
            //scrollTarget: false, // Set a custom target element for scrolling to the top
            scrollText: '<i class="fa fa-angle-up"></i>', // Text for element, can contain HTML
            scrollTitle: false, // Set a custom <a> title if required.
            scrollImg: false, // Set true to use image
            activeOverlay: false, // Set CSS color to display scrollUp active point, e.g '#00FFFF'
            zIndex: 2147483647, // Z-Index for the overlay
        });
    });
});
