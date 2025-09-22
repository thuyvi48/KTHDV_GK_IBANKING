function togglePassword(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);

  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
function enableEdit(fieldId) {
  document.getElementById(fieldId).removeAttribute("readonly");
  document.getElementById(fieldId).focus();
}

function submitField(field) {
  const input = document.getElementById(field);
  const originalValue = input.defaultValue; // Giá trị ban đầu của input
  const currentValue = input.value;

  if (originalValue === currentValue) {
    // Không thay đổi gì, không gửi form
    input.readOnly = true;
    return;
  }

  document.getElementById("fieldInput").value = field;
  document.getElementById("mainForm").submit();
  document.getElementById("fieldInput").remove();
}

document.addEventListener("DOMContentLoaded", function () {
  const toastEl = document.querySelector("#liveToast");
  if (toastEl) {
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const successToast = document.getElementById("successToast");
  if (successToast && typeof bootstrap !== "undefined") {
    const toast = new bootstrap.Toast(successToast, { delay: 3000 });
    toast.show();
  }
});

function confirmDelete(productId) {
  const modal = document.getElementById("deleteModal");
  modal.style.display = "flex";

  // Set up delete action
  document.getElementById("confirmDelete").onclick = function () {
    window.location.href = "cart.php?delete=" + productId;
  };

  // Set up cancel action
  document.getElementById("cancelDelete").onclick = function () {
    modal.style.display = "none";
  };

  // Close modal when clicking outside
  window.onclick = function (event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };
}
setTimeout(() => {
  const alertEl = document.querySelector(".alert");
  if (alertEl) {
    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
    bsAlert.close();
  }
}, 5000);
