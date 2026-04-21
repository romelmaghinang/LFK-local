<?php /* Template Name: Chatbot Page Template */ ?>

<?php get_header(); ?>

<style>
	.chat-container { display: flex; flex-direction: column; width: 100%; max-width: 600px; height: 90vh; background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; margin: 0 auto; margin-bottom: 50px; }
	.header { background: #ff9900; color: #fff; padding: 16px; text-align: center; font-size: 18px; font-weight: bold; }
	.header img { max-width: 300px; }
	.messages { flex: 1; overflow-y: auto; padding: 16px; display: flex; flex-direction: column; gap: 12px; background: #f9fafb; }
	.bubble-wrap { display: flex; }
	.bubble-wrap.user { justify-content: flex-end; }
	.bubble-wrap.assistant { justify-content: flex-start; }
	.bubble { max-width: 80%; padding: 10px 14px; border-radius: 16px; line-height: 1.5; white-space: pre-wrap; font-size: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
	.bubble.user { background: #ff9900 color: #fff; }
	.bubble.assistant { background: #fff; color: #111; }
	.bubble.typing { color: #888; }
	.input-area { display: flex; padding: 12px; gap: 8px; border-top: 1px solid #e5e7eb; background: #fff; }
	input { flex: 1; padding: 10px 14px; border-radius: 24px; border: 1px solid #d1d5db; outline: none; font-size: 14px; }
	button { padding: 10px 20px; border-radius: 24px; background: #ff9900; color: #fff; border: none; cursor: pointer; font-weight: bold; font-size: 14px; }
	button:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class='main-mid'>
<div class='_maxwrap'>

	<div class="chat-container">
		<div class="header"><img src="https://v2.lote4kids.com/wp-content/themes/lote4kids-child/assets/img/logo-main.svg" /></div>
		<div class="messages" id="messages">
		  <div class="bubble-wrap assistant">
		    <div class="bubble assistant">Hi! How can I help you today?</div>
		  </div>
		</div>
		<div class="input-area">
		  <input id="input" type="text" placeholder="Type a message..." />
		  <button id="sendBtn" onclick="sendMessage()">Send</button>
		</div>
	</div>

	<script>
		const history = [];
		const messagesEl = document.getElementById("messages");
		const inputEl = document.getElementById("input");
		const sendBtn = document.getElementById("sendBtn");

		inputEl.addEventListener("keydown", e => { if (e.key === "Enter") sendMessage(); });

		function addBubble(role, text) {
		  const wrap = document.createElement("div");
		  wrap.className = "bubble-wrap " + role;
		  const bubble = document.createElement("div");
		  bubble.className = "bubble " + role;
		  bubble.textContent = text;
		  wrap.appendChild(bubble);
		  messagesEl.appendChild(wrap);
		  messagesEl.scrollTop = messagesEl.scrollHeight;
		  return bubble;
		}

		async function sendMessage() {
		  const text = inputEl.value.trim();
		  if (!text) return;

		  inputEl.value = "";
		  sendBtn.disabled = true;
		  addBubble("user", text);
		  history.push({ role: "user", content: text });

		  const typingBubble = addBubble("assistant", "Typing...");
		  typingBubble.classList.add("typing");

		  try {
		    const res = await fetch("https://v2.lote4kids.com/claudechat", {
		      method: "POST",
		      headers: { "Content-Type": "application/json" },
		      body: JSON.stringify({ messages: history })
		    });

		    const data = await res.json();
		    const reply = data.content && data.content[0] ? data.content[0].text : "Sorry, no response.";
		    history.push({ role: "assistant", content: reply });
		    typingBubble.textContent = reply;
		    typingBubble.classList.remove("typing");
		  } catch (err) {
		    typingBubble.textContent = "Error: " + err.message;
		  }

		  sendBtn.disabled = false;
		  inputEl.focus();
		}
	</script>

</div>
</div>

<?php get_footer(); ?>