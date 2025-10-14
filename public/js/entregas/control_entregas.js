$(document).ready(function () {
  $("#example").DataTable();
});

const peruHolidays = [
  "2025-01-01",
  "2025-04-17",
  "2025-04-18",
  "2025-05-01",
  "2025-06-29",
  "2025-07-28",
  "2025-07-29",
  "2025-08-30",
  "2025-10-08",
  "2025-11-01",
  "2025-12-08",
  "2025-12-25",
];

const btnAdd = document.getElementById("btnAdd");
const formEntrega = document.getElementById("formEntrega");

btnAdd.addEventListener("click", function () {
  const myModal = new bootstrap.Modal(document.getElementById("modalEntrega"));
  myModal.show();
});

formEntrega.addEventListener("submit", function (e) {
  e.preventDefault();

  fetch("/entregas/guardar", {
    method: "POST",
    body: new FormData(formEntrega),
  })
    .then((response) => response.json())
    .then((data) => {
      alert("Error al guardar la entrega");
      /*if (data.success) {
                alert('Entrega guardada con éxito');
                location.reload();
            } else {
                alert('Error al guardar la entrega');
            }*/
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("Error al guardar la entrega");
    });
});

document
  .getElementById("fecha_hora_inicio")
  .addEventListener("change", function () {
    const startDateTime = this.value;
    const estimatedTime = document.getElementById("estimated-time").value;

    if (startDateTime && estimatedTime) {
      const calculatedDateTime = calculateWorkingHours(
        startDateTime,
        estimatedTime
      );
      document.getElementById("input6").value = calculatedDateTime;
    }
  });

document
  .getElementById("estimated-time")
  .addEventListener("input", function () {
    const startDateTime = document.getElementById("fecha_hora_inicio").value;
    const estimatedTime = this.value;

    if (
      startDateTime &&
      estimatedTime &&
      estimatedTime.match(/[0-9]{2}:[0-9]{2}/)
    ) {
      const calculatedDateTime = calculateWorkingHours(
        startDateTime,
        estimatedTime
      );
      document.getElementById("input6").value = calculatedDateTime;
    }
  });

function calculateWorkingHours(startDateTime, duration) {
  const start = new Date(startDateTime);
  const [durationHours, durationMinutes] = duration.split(":").map(Number);
  let remainingMinutes = durationHours * 60 + durationMinutes;

  let current = new Date(start);

  function getMinutesSinceMidnight(date) {
    return date.getHours() * 60 + date.getMinutes();
  }

  function setTimeFromMinutes(date, minutes) {
    date.setHours(Math.floor(minutes / 60));
    date.setMinutes(minutes % 60);
    date.setSeconds(0);
    date.setMilliseconds(0);
  }

  while (remainingMinutes > 0) {
    const dayOfWeek = current.getDay();
    const currentMinutes = getMinutesSinceMidnight(current);

    const dateStr = current.toISOString().split("T")[0];
    const isHoliday = peruHolidays.includes(dateStr);

    if (dayOfWeek === 0 || isHoliday) {
      current.setDate(current.getDate() + 1);
      current.setHours(8, 0, 0, 0);
      continue;
    }

    if (dayOfWeek >= 1 && dayOfWeek <= 5) {
      const morningStart = 8 * 60;
      const morningEnd = 13 * 60;
      const afternoonStart = 15 * 60;
      const afternoonEnd = 19 * 60;

      if (currentMinutes < morningStart) {
        setTimeFromMinutes(current, morningStart);
        continue;
      }

      if (currentMinutes >= morningStart && currentMinutes < morningEnd) {
        const availableInMorning = morningEnd - currentMinutes;

        if (remainingMinutes <= availableInMorning) {
          setTimeFromMinutes(current, currentMinutes + remainingMinutes);
          remainingMinutes = 0;
        } else {
          remainingMinutes -= availableInMorning;
          setTimeFromMinutes(current, afternoonStart);
        }
      } else if (
        currentMinutes >= morningEnd &&
        currentMinutes < afternoonStart
      ) {
        setTimeFromMinutes(current, afternoonStart);
        continue;
      } else if (
        currentMinutes >= afternoonStart &&
        currentMinutes < afternoonEnd
      ) {
        const availableInAfternoon = afternoonEnd - currentMinutes;

        if (remainingMinutes <= availableInAfternoon) {
          setTimeFromMinutes(current, currentMinutes + remainingMinutes);
          remainingMinutes = 0;
        } else {
          remainingMinutes -= availableInAfternoon;
          current.setDate(current.getDate() + 1);
          current.setHours(8, 0, 0, 0);
        }
      } else {
        current.setDate(current.getDate() + 1);
        current.setHours(8, 0, 0, 0);
      }
    } else if (dayOfWeek === 6) {
      const morningStart = 8 * 60;
      const morningEnd = 13 * 60;

      if (currentMinutes < morningStart) {
        setTimeFromMinutes(current, morningStart);
        continue;
      }

      if (currentMinutes >= morningStart && currentMinutes < morningEnd) {
        const availableInMorning = morningEnd - currentMinutes;

        if (remainingMinutes <= availableInMorning) {
          setTimeFromMinutes(current, currentMinutes + remainingMinutes);
          remainingMinutes = 0;
        } else {
          remainingMinutes -= availableInMorning;
          current.setDate(current.getDate() + 2);
          current.setHours(8, 0, 0, 0);
        }
      } else {
        current.setDate(current.getDate() + 2);
        current.setHours(8, 0, 0, 0);
      }
    }
  }

  const year = current.getFullYear();
  const month = String(current.getMonth() + 1).padStart(2, "0");
  const day = String(current.getDate()).padStart(2, "0");
  const hours = String(current.getHours()).padStart(2, "0");
  const minutes = String(current.getMinutes()).padStart(2, "0");

  return `${year}-${month}-${day}T${hours}:${minutes}`;
}
