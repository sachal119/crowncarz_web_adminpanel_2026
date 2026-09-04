@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
  <div class="row g-3">
    
    <!-- 🌐 Left Panel: Drivers -->
    <div class="col-md-4 col-lg-3">
      <div class="card shadow-sm border-0" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-gradient py-3" style="background: linear-gradient(90deg, #007bff, #6610f2);">
          <h5 class="mb-0 fw-semibold"><i class="bi bi-people-fill me-2"></i> Drivers</h5>
        </div>

        <div class="p-3 bg-light">
          <!-- 🔍 Search -->
          <div class="input-group mb-3">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" id="driverSearch" class="form-control shadow-none" placeholder="Search driver...">
          </div>

          <!-- 👨‍✈️ Driver List -->
          <div id="driverList" class="list-group" style="max-height: 70vh; overflow-y: auto;">
            <div class="text-center text-muted py-4">Loading drivers...</div>
          </div>
        </div>
      </div>
    </div>

    <!-- 💬 Right Panel: Chat Window -->
    <div class="col-md-8 col-lg-9">
      <div class="card shadow-sm border-0" style="border-radius: 20px; height: 82vh; display: flex; flex-direction: column;">
        
        <!-- Header -->
        <div class="card-header bg-white border-0 d-flex align-items-center justify-content-between shadow-sm" style="border-radius: 20px 20px 0 0;">
          <div class="d-flex align-items-center">
            <div id="chatDriverAvatar" class="me-3">
              <i class="bi bi-person-circle text-secondary fs-2"></i>
            </div>
            <div>
              <h6 id="chatTitle" class="mb-0 fw-bold text-dark">Select a Driver</h6>
              <small id="chatStatus" class="text-muted">No active chat</small>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div id="chatBox" class="flex-grow-1 px-4 py-3 bg-light position-relative" style="overflow-y: auto;">
          <div class="text-center text-muted mt-5">Select a driver to start chatting</div>
        </div>

        <!-- Message Input -->
        <div class="card-footer bg-white border-0 p-3 shadow-sm" style="border-radius: 0 0 20px 20px;">
          <div class="input-group">
            <input type="text" id="chatMessage" class="form-control shadow-none" placeholder="Type a message..." disabled>
            <button class="btn btn-primary px-4 fw-semibold" id="sendBtn" disabled>
              <i class="bi bi-send-fill"></i>
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
  /* Driver avatar pulse for online */
  .status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
  }
  .online-dot {
    background-color: #28a745;
    box-shadow: 0 0 8px #28a745;
    animation: pulse 1.5s infinite;
  }
  .offline-dot {
    background-color: #6c757d;
  }
  @keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.6); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
  }

  /* Chat bubbles */
  .chat-bubble {
    max-width: 75%;
    border-radius: 16px;
    padding: 10px 14px;
    margin-bottom: 10px;
    position: relative;
  }
  .chat-office {
    background: #007bff;
    color: white;
    margin-left: auto;
  }
  .chat-driver {
    background: #f1f0f0;
    color: #000;
  }
  .chat-time {
    font-size: 11px;
    opacity: 0.7;
    display: block;
    margin-top: 4px;
    text-align: right;
  }

  /* Scroll shadow effect */
  #chatBox::before {
    content: '';
    position: sticky;
    top: 0;
    height: 15px;
    background: linear-gradient(180deg, rgba(0,0,0,0.08), transparent);
    display: block;
  }
</style>

<!-- 🧠 Firebase Chat Logic -->
<script type="module">
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import { getDatabase, ref, onValue, push } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-database.js";

const firebaseConfig = {
  apiKey: "AIzaSyDf9Ujb0fAaE7CWAtkb5qCUJn5RDsRvPAQ",
  authDomain: "crown-carz.firebaseapp.com",
  databaseURL: "https://crown-carz-default-rtdb.firebaseio.com",
  projectId: "crown-carz",
  storageBucket: "crown-carz.firebasestorage.app",
  messagingSenderId: "854843072109",
  appId: "1:854843072109:web:dd5ff34f1ed03521d5a964",
  measurementId: "G-VCG3S5E63H"
};

const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

const driverList = document.getElementById("driverList");
const driverSearch = document.getElementById("driverSearch");
const chatBox = document.getElementById("chatBox");
const chatTitle = document.getElementById("chatTitle");
const chatStatus = document.getElementById("chatStatus");
const chatMessage = document.getElementById("chatMessage");
const sendBtn = document.getElementById("sendBtn");
const chatDriverAvatar = document.getElementById("chatDriverAvatar");

let currentDriverId = null;
let allDrivers = {};

const driversRef = ref(db, "drivers");
onValue(driversRef, (snapshot) => {
  if (!snapshot.exists()) {
    driverList.innerHTML = `<div class="text-center text-muted py-3">No drivers found.</div>`;
    return;
  }
  allDrivers = snapshot.val();
  renderDrivers(Object.entries(allDrivers));
});

function renderDrivers(drivers) {
  const query = driverSearch.value.toLowerCase();
  driverList.innerHTML = "";
  const filtered = drivers.filter(([id, d]) =>
    (d.name && d.name.toLowerCase().includes(query)) ||
    (d.phone && d.phone.toLowerCase().includes(query))
  );

  if (filtered.length === 0) {
    driverList.innerHTML = `<div class="text-center text-muted py-3">No matching drivers.</div>`;
    return;
  }

  filtered.forEach(([id, d]) => {
    const isOnline = d.is_online === true || d.status === "online";
    const div = document.createElement("div");
    div.className = "list-group-item list-group-item-action d-flex justify-content-between align-items-center";
    div.style.cursor = "pointer";
    div.innerHTML = `
      <div>
        <div class="fw-semibold">${d.name || "Unnamed Driver"}</div>
        <small class="text-muted">${d.phone || ""}</small>
      </div>
      <div class="d-flex align-items-center">
        <span class="status-dot ${isOnline ? "online-dot" : "offline-dot"} me-2"></span>
        <span class="badge ${isOnline ? "bg-success" : "bg-secondary"}">${isOnline ? "Online" : "Offline"}</span>
      </div>
    `;
    div.onclick = () => openChat(id, d.name, isOnline);
    driverList.appendChild(div);
  });
}

driverSearch.addEventListener("input", () => renderDrivers(Object.entries(allDrivers)));

function openChat(driverId, driverName, isOnline) {
  currentDriverId = driverId;
  chatTitle.textContent = driverName;
  chatStatus.textContent = isOnline ? "🟢 Online" : "⚫ Offline";
  chatDriverAvatar.innerHTML = `<i class="bi bi-person-circle text-${isOnline ? 'success' : 'secondary'} fs-2"></i>`;
  chatMessage.disabled = false;
  sendBtn.disabled = false;
  chatBox.innerHTML = "";

  const chatRef = ref(db, `office_to_driver/${driverId}`);
  onValue(chatRef, (snapshot) => {
    chatBox.innerHTML = "";
    if (!snapshot.exists()) {
      chatBox.innerHTML = `<div class="text-center text-muted mt-5">No messages yet</div>`;
      return;
    }

    const messages = Object.values(snapshot.val());
    messages.forEach((msg) => {
      const msgDiv = document.createElement("div");
      msgDiv.classList.add("chat-bubble", msg.sender === "office" ? "chat-office ms-auto" : "chat-driver me-auto");
      msgDiv.innerHTML = `
        ${msg.text}
        <span class="chat-time">${new Date(msg.timestamp).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}</span>
      `;
      chatBox.appendChild(msgDiv);
    });
    chatBox.scrollTop = chatBox.scrollHeight;
  });
}

sendBtn.addEventListener("click", async () => {
  if (!chatMessage.value.trim() || !currentDriverId) return;
  const messageRef = ref(db, `office_to_driver/${currentDriverId}`);
  await push(messageRef, {
    sender: "office",
    text: chatMessage.value,
    timestamp: Date.now()
  });
  chatMessage.value = "";
});
</script>
@endsection
