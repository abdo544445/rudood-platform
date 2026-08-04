const express = require('express');
const http    = require('http');
const { Server } = require('socket.io');
const Redis   = require('ioredis');

const app    = express();
const server = http.createServer(app);
const io     = new Server(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  }
});

// Redis subscriber (separate connection from publisher)
const redisSub = new Redis();

// Subscribe to the Laravel-published channel
redisSub.subscribe('rudood_chat_channel', (err, count) => {
  if (err) {
    console.error('[Redis] Failed to subscribe:', err.message);
  } else {
    console.log(`[Redis] Subscribed to ${count} channel(s).`);
  }
});

// When Laravel publishes a message, broadcast it to the correct workspace room
redisSub.on('message', (channel, message) => {
  try {
    const data = JSON.parse(message);
    const workspaceRoom = `workspace_${data.workspace_id}`;

    console.log(`[Redis] -> ${workspaceRoom}:`, data);

    // Broadcast only to clients in the correct workspace room
    io.to(workspaceRoom).emit('new_message', data);

    // Also emit a sidebar update event so the conversation list refreshes
    io.to(workspaceRoom).emit('conversation_updated', {
      conversation_id: data.conversation_id,
      last_message:    data.content,
      time:            data.time,
    });

  } catch (e) {
    console.error('[Redis] Error parsing message:', e.message);
  }
});

// Socket.io connection handling
io.on('connection', (socket) => {
  console.log(`[Socket] Client connected: ${socket.id}`);

  // Client sends their workspace_id to join the correct room
  socket.on('join_workspace', (workspace_id) => {
    const room = `workspace_${workspace_id}`;
    socket.join(room);
    console.log(`[Socket] ${socket.id} joined room: ${room}`);
  });

  // Client joins a specific conversation room (for typing indicators etc.)
  socket.on('join_conversation', (conversation_id) => {
    socket.join(`conversation_${conversation_id}`);
  });

  socket.on('disconnect', () => {
    console.log(`[Socket] Client disconnected: ${socket.id}`);
  });
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({ status: 'ok', uptime: process.uptime() });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`[Server] WebSocket server running on port ${PORT}`);
});
