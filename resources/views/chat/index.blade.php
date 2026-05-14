@extends('layouts.app')
@section('title', 'Chat')

@section('content')
<div class="h-[calc(100vh-4rem)] flex" x-data="chatApp()">
    <!-- Channels sidebar -->
    <div class="w-64 flex-shrink-0 bg-white dark:bg-[#0f0f1a] border-r border-gray-100 dark:border-white/5 flex flex-col">
        <div class="p-4 border-b border-gray-100 dark:border-white/5">
            <h2 class="font-bold text-gray-900 dark:text-white">Chat</h2>
        </div>
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            @foreach($channels as $channel)
            <button @click="selectChannel({{ $channel->id }}, '{{ $channel->name }}')"
                    :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400': activeChannel === {{ $channel->id }} }"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-white/5 transition text-left">
                <span class="text-lg">#</span>
                <span class="text-sm font-medium">{{ $channel->name }}</span>
            </button>
            @endforeach
        </nav>
    </div>

    <!-- Chat area -->
    <div class="flex-1 flex flex-col">
        <!-- Channel header -->
        <div class="h-14 border-b border-gray-100 dark:border-white/5 px-5 flex items-center gap-3 bg-white dark:bg-[#0f0f1a]">
            <span class="text-indigo-500 font-bold text-lg">#</span>
            <span class="font-semibold text-gray-900 dark:text-white" x-text="activeChannelName || 'Select a channel'"></span>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-5 space-y-4" id="messagesContainer">
            <template x-if="messages.length === 0">
                <div class="text-center text-gray-400 py-10">
                    <p>Belum ada pesan. Mulai percakapan!</p>
                </div>
            </template>
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex items-start gap-3">
                    <img :src="msg.user.avatar_url || `https://ui-avatars.com/api/?name=${msg.user.name}&background=6366f1&color=fff`"
                         :alt="msg.user.name" class="w-8 h-8 rounded-full flex-shrink-0 mt-0.5">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white" x-text="msg.user.name"></span>
                            <span class="text-xs text-gray-400" x-text="msg.time"></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed" x-text="msg.content"></p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Message input -->
        <div class="p-4 border-t border-gray-100 dark:border-white/5 bg-white dark:bg-[#0f0f1a]">
            <div class="flex items-center gap-3 bg-gray-50 dark:bg-[#13132a] border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3">
                <input type="text" x-model="newMessage" @keydown.enter="sendMessage()"
                    :placeholder="activeChannel ? `Message #${activeChannelName}...` : 'Select a channel first'"
                    class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 outline-none">
                <button @click="sendMessage()" :disabled="!activeChannel || !newMessage.trim()"
                    class="p-1.5 rounded-lg text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition disabled:opacity-30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chatApp() {
    return {
        activeChannel: null,
        activeChannelName: '',
        messages: [],
        newMessage: '',

        async selectChannel(id, name) {
            this.activeChannel = id;
            this.activeChannelName = name;
            await this.loadMessages();
        },

        async loadMessages() {
            if (!this.activeChannel) return;
            // Simulated messages from seeded data
            this.messages = [
                { id: 1, user: { name: 'Super Admin', avatar_url: '' }, content: 'Selamat datang di NexaCRM! 🎉', time: '10:00' },
                { id: 2, user: { name: 'Budi Santoso', avatar_url: '' }, content: 'Siap! Project sudah berjalan.', time: '10:05' },
                { id: 3, user: { name: 'Andi Wijaya', avatar_url: '' }, content: 'Wireframe sudah selesai! 👍', time: '10:10' },
            ];
            this.$nextTick(() => {
                const el = document.getElementById('messagesContainer');
                el.scrollTop = el.scrollHeight;
            });
        },

        async sendMessage() {
            if (!this.activeChannel || !this.newMessage.trim()) return;
            const content = this.newMessage;
            this.newMessage = '';

            this.messages.push({
                id: Date.now(),
                user: { name: '{{ auth()->user()->name }}', avatar_url: '{{ auth()->user()->avatar_url }}' },
                content: content,
                time: new Date().toLocaleTimeString('id', { hour: '2-digit', minute: '2-digit' }),
            });

            this.$nextTick(() => {
                const el = document.getElementById('messagesContainer');
                el.scrollTop = el.scrollHeight;
            });
        }
    }
}
</script>
@endpush
