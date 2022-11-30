function incrementQty() {
  var value = document.querySelector('input[name="qty"]').value;
  var cardQty = document.querySelector(".cart-qty");
  value = isNaN(value) ? 1 : value;
  value++;
  document.querySelector('input[name="qty"]').value = value;
  cardQty.innerHTML = value;
  cardQty.classList.add("rotate-x");
}

function decrementQty() {
  var value = document.querySelector('input[name="qty"]').value;
  var cardQty = document.querySelector(".cart-qty");
  value = isNaN(value) ? 1 : value;
  value > 1 ? value-- : value;
  document.querySelector('input[name="qty"]').value = value;
  cardQty.innerHTML = value;
  cardQty.classList.add("rotate-x");
}

function removeAnimation(e) {
  e.target.classList.remove("rotate-x");
}

const counter = document.querySelector(".cart-qty");
counter.addEventListener("animationend", removeAnimation);