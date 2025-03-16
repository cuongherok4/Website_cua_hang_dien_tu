document.addEventListener("DOMContentLoaded", function () {
    const addToCartBtn = document.getElementById("addToCartBtn");
    const quantityInput = document.querySelector(".quantity input");

    if (!addToCartBtn || !quantityInput) {
        console.error("Lỗi: Không tìm thấy phần tử cần thiết!");
        return;
    }

    addToCartBtn.addEventListener("click", function () {
        const maSP = new URLSearchParams(window.location.search).get("id");
        const soLuong = parseInt(quantityInput.value);

        if (!maSP) {
            alert("Lỗi: Không tìm thấy mã sản phẩm!");
            return;
        }

        if (soLuong < 1 || isNaN(soLuong)) {
            alert("Vui lòng chọn số lượng hợp lệ.");
            return;
        }

        fetch("/HMC/web/them_giohang.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `maSP=${encodeURIComponent(maSP)}&soLuong=${encodeURIComponent(soLuong)}`
        })
        .then(response => response.json())
        .then(data => {
            console.log("Phản hồi từ server:", data);
            alert(data.message);
            if (data.success) {
                window.location.href = "/HMC/web/giohang.php"; // Chuyển hướng đến giỏ hàng
            }
        })
        .catch(error => {
            console.error("Lỗi:", error);
            alert("Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng.");
        });
    });
});
