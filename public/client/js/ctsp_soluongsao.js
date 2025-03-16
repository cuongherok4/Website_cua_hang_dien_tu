document.addEventListener("DOMContentLoaded", function () {
    const minusBtn = document.querySelector(".minus");
    const plusBtn = document.querySelector(".plus");
    const quantityInput = document.querySelector(".quantity input");

    // Giảm số lượng
    minusBtn.addEventListener("click", function () {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    // Tăng số lượng
    plusBtn.addEventListener("click", function () {
        let currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
    });

    const stars = document.querySelectorAll('.star');
    stars.forEach((star, index) => {
        star.addEventListener('click', function () {
            // Cập nhật các ngôi sao đã chọn
            for (let i = 0; i < stars.length; i++) {
                if (i <= index) {
                    stars[i].classList.add('filled');
                } else {
                    stars[i].classList.remove('filled');
                }
            }
        });

        star.addEventListener('mouseenter', function () {
            // Sáng tất cả các ngôi sao trước đó khi di chuột vào
            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('filled');
            }
        });

        star.addEventListener('mouseleave', function () {
            // Tắt sáng tất cả các ngôi sao khi chuột rời đi
            for (let i = 0; i < stars.length; i++) {
                if (!stars[i].classList.contains('filled')) {
                    stars[i].classList.remove('filled');
                }
            }
        });
    });
});
