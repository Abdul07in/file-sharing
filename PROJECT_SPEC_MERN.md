# MERN Stack Project Specification & Migration Plan

This document details the features of the current File Sharing & Collaboration platform and outlines the technical specification for migrating it to a modern MERN (MongoDB, Express, React, Node.js) stack.

## 🌟 Core Features

### 1. 🔐 User Authentication & Management
*   **Current Specification**: Session-based login/registration using PHP/MySQL.
*   **MERN Improvement**:
    *   **JWT (JSON Web Tokens)** for stateless, secure authentication.
    *   **OAuth Integration** (optional): Allow login via Google/GitHub.
    *   **User Profiles**: Avatars, persistent settings.

### 2. 📁 File Sharing
*   **Current Specification**: HTTP-based upload/download via `FileController`.
*   **MERN Improvement**:
    *   **Chunked Uploads**: For handling large files efficiently.
    *   **Drag & Drop Interface**: React Dropzone implementation.
    *   **P2P Transfer (WebRTC)**: Option to send files directly between users (skipping server storage for privacy/speed) using `simple-peer` Data Channels.
    *   **Ephemeral Storage**: Auto-delete files after 24 hours (Cron jobs).

### 3. 📝 Real-Time Text Collaboration
*   **Current Specification**: Cooperative editing using **YJS** and **CodeMirror**, synced via a custom MockWebSocket (polling PHP backend).
*   **MERN Improvement**:
    *   **True Real-Time**: Replace polling with **Socket.IO** (WebSockets) for instant syncing (sub-100ms latency).
    *   **Y-Websocket / Y-Socket.io**: Standardized YJS provider for robust conflict resolution.
    *   **Awareness**: Show other users' cursors and selection highlights in real-time.
    *   **Markdown Preview**: Live split-pane preview of Markdown content.

### 4. 📹 Video & Audio Conferencing
*   **Current Specification**: Custom `SimpleWebRTC` implementation using Polling for signaling.
*   **MERN Improvement**:
    *   **Socket.IO Signaling**: Instant connection establishment (no more "glare" or connection delays).
    *   **Mesh Network**: Support multiple peers (up to 4-6) reliably.
    *   **Controls**: Mute/Unmute, Video On/Off, Screen Sharing.
    *   **Connection Status**: Visual indicators for connection quality.

### 5. 🏠 Room Management
*   **Current Specification**: Database-backed rooms with simple unique Keys.
*   **MERN Improvement**:
    *   **Dynamic Routing**: URLs like `/room/:roomId`.
    *   **Room Security**: Password-protected rooms or Waiting Rooms.
    *   **Dashboard**: History of joined rooms for logged-in users.

---

## 🛠️ Technical Stack & Migration Path

### Frontend (Client)
*   **Framework**: React 19 (via Vite)
*   **State Management**: Zustand or React Context
*   **Styling**: Tailwind CSS + ShadcnUI (for premium aesthetics)
*   **Editor**: `@codemirror/dev`
*   **WebRTC**: `simple-peer` or `marketing-conferencing-api` wrappers
*   **Deployment**: Vercel (Free Tier)

### Backend (Server)
*   **Runtime**: Node.js
*   **Framework**: Express.js
*   **Real-Time Engine**: Socket.IO
*   **Database**: MongoDB (via Mongoose)
    *   *Why MongoDB?* Perfect for storing unstructured JSON documents (YJS updates) and flexible user schemas.
*   **Deployment**: Render.com (Free Web Service - supports Persistent Sockets) or Railway.

---

## 📋 Database Schema (Proposed)

```javascript
// User Schema
const UserSchema = new Schema({
  username: { type: String, required: true },
  email: { type: String, unique: true },
  password: { type: String }, // Hashed
  avatar: String
});

// Room Schema
const RoomSchema = new Schema({
  roomId: { type: String, unique: true },
  name: String,
  owner: { type: Schema.Types.ObjectId, ref: 'User' },
  isPrivate: Boolean,
  password: String, // Optional
  createdAt: { type: Date, default: Date.now }
});

// Document Schema (Collaboration)
const DocumentSchema = new Schema({
  roomId: String,
  content: Buffer, // YJS Binary Update
  lastModified: Date
});
```

## 🚀 Deployment Strategy (Free Tier)

Since you want to deploy for free:
1.  **Frontend -> Vercel**: Connect your GitHub repo. Vercel handles SSL, CDN, and React routing automatically.
2.  **Backend -> Render**: Deploy the Node.js/Express app here. Render provides free TLS and supports the long-lived connections required for Socket.IO (unlike Vercel Serverless).
3.  **Database -> MongoDB Atlas**: Use the free M0 tier (512MB storage), which is sufficient for metadata and text content.

## 📅 Roadmap

1.  **Setup**: Initialize Repo, Frontend (Vite), Backend (Express).
2.  **Auth**: Implement Login/Register APIs.
3.  **Sockets**: Setup Socket.IO server and client connection.
4.  **Rooms**: Create/Join logic.
5.  **Collab**: Hook up YJS with Socket.IO.
6.  **WebRTC**: Implement video chat using the signaling server.
7.  **Polish**: Add Tailwind styles, dark mode, and responsive layouts.
8.  **Deploy**: Push to Vercel/Render.
