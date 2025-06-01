<template>
  <div class="download-page">
    <div class="container">
      <div class="download-card card">
        <h1 class="page-title">Lejupielādes</h1>

        <div class="download-links">
          <div v-if="downloadableFiles.length === 0" class="no-files-message">
            Pašlaik nav lejupielādējamu failu
          </div>
          <div
            class="download-item"
            v-for="(file, index) in downloadableFiles"
            :key="index"
          >
            <div class="file-info">
              <span class="file-name">{{ file.name }}</span>
              <span class="file-size" v-if="file.size">{{ formatFileSize(file.size) }}</span>
            </div>
            <a :href="file.url" download class="btn btn-primary download-btn">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
              </svg>
              Lejupielādēt
            </a>
            <button v-if="isAdmin" @click="deleteFile(file, index)" class="btn btn-danger delete-btn">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
              </svg>
              Izdzēst
            </button>
          </div>
        </div>

        <div v-if="isAdmin" class="admin-section">
          <h2 class="section-title">Administratora augšupielādēšana</h2>
          <div class="upload-container">
            <div class="file-input-wrapper">
              <input type="file" id="file-upload" class="file-input" @change="handleFileUpload" />
              <label for="file-upload" class="file-label">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                  <polyline points="17 8 12 3 7 8"/>
                  <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                {{ selectedFile ? selectedFile.name : 'Izvēlies failu' }}
              </label>
            </div>
            <button @click="uploadFile" class="btn btn-primary upload-btn" :disabled="!selectedFile">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
              Augšupielādēt
            </button>
          </div>

          <div v-if="uploadMessage" class="alert" :class="{'alert-success': uploadSuccess, 'alert-error': !uploadSuccess}">
            {{ uploadMessage }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "DownloadPage",
  data() {
    return {
      selectedFile: null,
      uploadMessage: '',
      uploadSuccess: false,
      downloadableFiles: [],
      isAdmin: false
    };
  },
  mounted() {
    this.fetchDownloadableFiles();
    this.checkUserRole();
  },
  methods: {
    async checkUserRole() {
      const token = localStorage.getItem('authToken');
      if (token) {
        try {
          const res = await axios.get('/api/getUserProfile', {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.isAdmin = res.data.userData.role_id === 1;
        } catch (err) {
          console.error('Error checking user role:', err);
        }
      }
    },
    async fetchDownloadableFiles() {
      try {
        const res = await axios.get('/api/getDownloadableFiles');
        this.downloadableFiles = res.data.files;
      } catch (err) {
        console.error('Kļūda atgriežot failus:', err);
      }
    },
    handleFileUpload(event) {
      try {
        this.selectedFile = event.target.files[0];
      } catch (err) {
        console.error("Error selecting file:", err);
        this.uploadMessage = 'Kļūda izvēloties failu.';
        this.uploadSuccess = false;
      }
    },
    async uploadFile() {
      const token = localStorage.getItem('authToken');
      if (!this.selectedFile) {
        this.uploadMessage = 'Lūdzu izvēlieties failu.';
        this.uploadSuccess = false;
        return;
      }
      const formData = new FormData();
      formData.append('file', this.selectedFile);

      try {
        const res = await axios.post('/api/uploadDownloadableFile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            Authorization: `Bearer ${token}`
          }
        });
        this.uploadMessage = res.data.message;
        this.uploadSuccess = true;
        this.fetchDownloadableFiles();
        this.selectedFile = null;
      } catch (err) {
        console.error("Upload error:", err);
        this.uploadMessage = err.response?.data?.message || 'Faila augšupielāde neizdevās. Lūdzu, mēģiniet vēlreiz.';
        this.uploadSuccess = false;
      }
    },
    async deleteFile(file, index) {
      const token = localStorage.getItem('authToken');
      if (confirm(`Vai tiešām vēlaties izdzēst? ${file.name}?`)) {
        try {
          const res = await axios.post('/api/deleteDownloadableFile', { url: file.url }, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.uploadMessage = res.data.message;
          this.uploadSuccess = res.data.status;
          if (res.data.status) {
            this.downloadableFiles.splice(index, 1);
          }
        } catch (err) {
          console.error("Delete error:", err);
          this.uploadMessage = err.response?.data?.message || 'Faila dzēšana neizdevās. Lūdzu, mēģiniet vēlreiz.';
          this.uploadSuccess = false;
        }
      }
    },
    formatFileSize(size) {
      if (size < 1024) return `${size} B`;
      else if (size < 1024 * 1024) return `${(size / 1024).toFixed(2)} KB`;
      else return `${(size / (1024 * 1024)).toFixed(2)} MB`;
    }
  }
};
</script>

<style scoped>
.download-page {
  min-height: 100vh;
  padding-top: 80px;
  background-color: var(--color-background);
}

.container {
  padding: var(--space-xl) var(--space-md);
  max-width: 800px;
  margin: 0 auto;
}

.download-card {
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--space-xl);
}

.page-title {
  font-size: var(--font-size-xxl);
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: var(--space-xl);
  text-align: center;
}

.section-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: var(--space-md);
  padding-bottom: var(--space-sm);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.download-links {
  margin-bottom: var(--space-xl);
}

.no-files-message {
  text-align: center;
  color: var(--color-text-secondary);
  font-style: italic;
  margin-bottom: var(--space-md);
}

.download-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--space-md);
  border-radius: var(--border-radius-md);
  background-color: rgba(255, 255, 255, 0.05);
  margin-bottom: var(--space-md);
  transition: all 0.3s ease;
}

.download-item:hover {
  background-color: rgba(255, 255, 255, 0.1);
  transform: translateY(-2px);
}

.file-info {
  display: flex;
  flex-direction: column;
  flex: 1;
}

.file-name {
  font-size: var(--font-size-md);
  font-weight: 500;
  color: var(--color-text-primary);
}

.file-size {
  font-size: var(--font-size-sm);
  color: var(--color-text-secondary);
  margin-top: var(--space-xs);
}

.download-btn, .delete-btn {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  margin-left: var(--space-md);
}

.admin-section {
  margin-top: var(--space-xl);
  padding-top: var(--space-lg);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.upload-container {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  margin-bottom: var(--space-lg);
}

.file-input-wrapper {
  flex: 1;
  position: relative;
}

.file-input {
  position: absolute;
  top: 0;
  left: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
  z-index: 2;
}

.file-label {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  padding: var(--space-md);
  background-color: var(--color-surface);
  border: 1px dashed var(--color-border);
  border-radius: var(--border-radius-md);
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.file-label:hover {
  border-color: var(--color-primary);
  background-color: rgba(255, 255, 255, 0.05);
}

.upload-btn {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.alert {
  padding: var(--space-md);
  border-radius: var(--border-radius-md);
  margin-top: var(--space-md);
}

.alert-success {
  background-color: rgba(76, 175, 80, 0.1);
  color: #4caf50;
}

.alert-error {
  background-color: rgba(244, 67, 54, 0.1);
  color: #f44336;
}

.icon {
  width: 18px;
  height: 18px;
}

@media (max-width: 768px) {
  .download-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .file-info {
    margin-bottom: var(--space-md);
    width: 100%;
  }

  .download-btn, .delete-btn {
    margin-left: 0;
    width: 100%;
    justify-content: center;
  }

  .download-btn {
    margin-bottom: var(--space-sm);
  }

  .upload-container {
    flex-direction: column;
  }

  .file-input-wrapper {
    width: 100%;
  }

  .upload-btn {
    width: 100%;
  }
}
</style>
