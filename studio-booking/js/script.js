// Fungsi untuk menangani form validasi
document.addEventListener("DOMContentLoaded", function () {
  // Validasi form login dan register
  const forms = document.querySelectorAll("form");

  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      const inputs = this.querySelectorAll(
        "input[required], select[required], textarea[required]"
      );
      let valid = true;

      inputs.forEach((input) => {
        if (!input.value.trim()) {
          valid = false;
          input.style.borderColor = "red";
        } else {
          input.style.borderColor = "";
        }
      });

      if (!valid) {
        e.preventDefault();
        alert("Harap isi semua field yang wajib diisi.");
      }
    });
  });

  // Tanggal minimal untuk input date
  const dateInputs = document.querySelectorAll('input[type="date"]');
  const today = new Date().toISOString().split("T")[0];

  dateInputs.forEach((input) => {
    if (!input.min) {
      input.min = today;
    }
  });

  // Time input validation
  const timeInputs = document.querySelectorAll('input[type="time"]');

  timeInputs.forEach((input) => {
    input.addEventListener("change", function () {
      const minTime = this.min;
      const maxTime = this.max;
      const value = this.value;

      if (value < minTime) {
        this.value = minTime;
      } else if (value > maxTime) {
        this.value = maxTime;
      }
    });
  });
});

// Fungsi untuk menampilkan modal
function showModal(modalId) {
  document.getElementById(modalId).style.display = "block";
}

// Fungsi untuk menyembunyikan modal
function hideModal(modalId) {
  document.getElementById(modalId).style.display = "none";
}

// Tutup modal ketika klik di luar
window.addEventListener("click", function (event) {
  const modals = document.querySelectorAll(".modal");

  modals.forEach((modal) => {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  });
});

// Fungsi untuk menghitung total harga booking
function calculateBookingTotal() {
  const studioSelect = document.getElementById("studio_id");
  const startTime = document.getElementById("start_time");
  const endTime = document.getElementById("end_time");
  const durationSpan = document.getElementById("duration");
  const totalPriceSpan = document.getElementById("total_price");

  if (studioSelect.value && startTime.value && endTime.value) {
    const pricePerHour = parseFloat(
      studioSelect.options[studioSelect.selectedIndex].getAttribute(
        "data-price"
      )
    );

    // Calculate duration in hours
    const start = new Date("2000-01-01T" + startTime.value);
    const end = new Date("2000-01-01T" + endTime.value);
    const duration = (end - start) / (1000 * 60 * 60);

    if (duration > 0) {
      durationSpan.textContent = duration.toFixed(1);
      totalPriceSpan.textContent =
        "Rp " + (pricePerHour * duration).toLocaleString();
    } else {
      durationSpan.textContent = "0";
      totalPriceSpan.textContent = "Rp 0";
    }
  }
}

// Event listeners untuk input booking
document.addEventListener("DOMContentLoaded", function () {
  const studioSelect = document.getElementById("studio_id");
  const startTime = document.getElementById("start_time");
  const endTime = document.getElementById("end_time");

  if (studioSelect) {
    studioSelect.addEventListener("change", calculateBookingTotal);
  }

  if (startTime) {
    startTime.addEventListener("change", calculateBookingTotal);
  }

  if (endTime) {
    endTime.addEventListener("change", calculateBookingTotal);
  }
});
