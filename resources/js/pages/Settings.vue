<template>
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-white mb-8">System Settings</h2>

        <!-- Media Config -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-lg mb-8">
            <div class="p-6 border-b border-slate-700 bg-slate-800/50">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-video text-purple-400 mr-3"></i> Display Media
                </h3>
                <p class="text-slate-400 text-sm mt-1">Manage the video content shown on the public TV display.</p>
            </div>
            
            <div class="p-6">
                <!-- Current Video Preview -->
                <div v-if="currentVideo" class="mb-6">
                    <label class="block text-sm font-bold text-slate-300 mb-2">Current Video</label>
                    <div class="aspect-video bg-black rounded-lg overflow-hidden border border-slate-600 relative group">
                        <video :src="currentVideoUrl" controls class="w-full h-full object-contain"></video>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 font-mono">{{ currentVideo }}</p>
                </div>

                <!-- Upload Form -->
                <form @submit.prevent="uploadVideo" class="mt-6">
                    <label class="block text-sm font-bold text-slate-300 mb-2">Upload New Video (MP4)</label>
                    <div class="flex items-center space-x-4">
                        <input type="file" ref="videoInput" accept="video/mp4,video/x-m4v,video/*" class="block w-full text-sm text-slate-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-purple-600 file:text-white
                            hover:file:bg-purple-700
                            cursor-pointer"
                        >
                        <button type="submit" :disabled="uploading" class="bg-purple-600 hover:bg-purple-500 text-white px-6 py-2 rounded-lg font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                            <i v-if="uploading" class="fa-solid fa-circle-notch fa-spin mr-2"></i>
                            {{ uploading ? 'Uploading...' : 'Upload' }}
                        </button>
                    </div>
                    <p v-if="message" :class="messageType === 'success' ? 'text-green-400' : 'text-red-400'" class="mt-3 text-sm font-bold">
                        {{ message }}
                    </p>
                </form>
            </div>
        </div>

        <!-- Text Config (Optional Placeholder) -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden shadow-lg opacity-60">
            <div class="p-6 border-b border-slate-700">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <i class="fa-solid fa-font text-blue-400 mr-3"></i> Running Text
                </h3>
            </div>
            <div class="p-6">
                <p class="text-slate-500 italic">Configuration for running text is coming soon.</p>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            currentVideo: null,
            uploading: false,
            message: '',
            messageType: ''
        }
    },
    computed: {
        currentVideoUrl() {
            return this.currentVideo ? `/assets/media/${this.currentVideo}` : '';
        }
    },
    mounted() {
        this.fetchSettings();
    },
    methods: {
        async fetchSettings() {
            try {
                const response = await axios.get('/api/admin/settings');
                if (response.data.video) {
                    this.currentVideo = response.data.video.value;
                }
            } catch (error) {
                console.error("Error fetching settings", error);
            }
        },
        async uploadVideo() {
            const file = this.$refs.videoInput.files[0];
            if (!file) {
                this.message = "Please select a video file.";
                this.messageType = 'error';
                return;
            }

            this.uploading = true;
            this.message = '';

            let formData = new FormData();
            formData.append('video', file);

            try {
                const response = await axios.post('/api/admin/settings/media', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
                this.message = 'Video uploaded successfully!';
                this.messageType = 'success';
                this.currentVideo = response.data.filename;
                this.$refs.videoInput.value = null;
            } catch (error) {
                this.message = 'Upload failed. Check file size max 20MB.';
                this.messageType = 'error';
            } finally {
                this.uploading = false;
            }
        }
    }
}
</script>
