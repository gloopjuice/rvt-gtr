<template>
  <div>
    <div class="forum-container">
      <div class="content-wrapper">
        <div class="header-section">
          <h1 class="header-title">Forums</h1>
          
          <div class="forum-actions">
            <div class="search-and-sort">
              <div class="search-bar">
                <input 
                  v-model="searchQuery" 
                  @input="searchPosts" 
                  type="text" 
                  placeholder="Meklēt ierakstu" 
                  class="search-input"
                />
                <span class="search-icon">🔍</span>
              </div>

              <div class="sort-controls">
                <select v-model="sortBy" @change="sortPosts" class="sort-dropdown">
                  <option value="newest">Pēc jaunākā</option>
                  <option value="oldest">Pēc vecākā</option>
                </select>
              </div>
            </div>
            
            <button @click="showModal = true" class="create-post-button btn btn-primary">
              <span class="icon">+</span> Jauns ieraksts
            </button>
          </div>
        </div>

        <div class="posts-container">
          <div v-if="sortedPosts.length === 0" class="no-posts">
            <div class="empty-state">
              <div class="empty-icon">📝</div>
              <h3>Nav vēl ierakstu</h3>
              <p>Esi pirmais kurš izveido ierakstu!</p>
              <button @click="showModal = true" class="btn btn-primary">Izveidot ierakstu</button>
            </div>
          </div>
          
          <ul v-else class="post-list">
            <li v-for="post in sortedPosts" :key="post.id" class="post-card" @click="navigateToPost(post.id)">
              <div class="post-content">
                <h3 class="post-title">{{ post.title }}</h3>
                <p class="post-excerpt" v-if="post.content">{{ truncateContent(post.content) }}</p>
                <div class="post-meta">
                  <div class="post-author">
                    <span class="author-avatar">{{ getInitials(post.author_name) }}</span>
                    <span>{{ post.author_name }}</span>
                  </div>
                  <span class="post-date">{{ formatTimeAgo(post.updated_at) }}</span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      
      <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title">Izveidot</h2>
            <button class="modal-close" @click="showModal = false">&times;</button>
          </div>
          
          <form @submit.prevent="createNewPost" class="post-form">
            <div class="form-control">
              <label for="post-title">Virsraksts</label>
              <input 
                type="text" 
                id="post-title"
                v-model="newPostTitle" 
                placeholder="Ieraksti virsrakstu" 
                class="form-input" 
                required
              >
            </div>
            
            <div class="form-control">
              <label for="post-content">Apakšraksts</label>
              <textarea 
                id="post-content"
                v-model="newPostContent" 
                placeholder="Ievadi apakšrakstu..." 
                class="form-textarea" 
                rows="6"
                required
              ></textarea>
            </div>
            
            <div class="form-actions">
              <button type="button" class="btn btn-secondary" @click="showModal = false">Atcelt</button>
              <button type="submit" class="btn btn-primary">Publicēt</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "ForumsHubView",
  components: { },
  data() {
    return {
      posts: [],
      sortedPosts: [],
      searchQuery: '',
      newPostTitle: '',
      newPostContent: '',
      sortBy: 'newest',
      showModal: false,
      errorMessage: '',
      showError: false,
      isLoading: false
    };
  },
  methods: {
    async getForumPosts() {
      try {
        const response = await axios.get('/api/forum-posts', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`
          }
        });
        this.posts = response.data.data || [];
        this.sortPosts();
      } catch (error) {
        console.error('Error fetching forum posts:', error);
        this.showErrorMessage('Unable to load forum posts. Please try again later.');
      }
    },
    sortPosts() {
      if (this.sortBy === 'oldest') {
        this.sortedPosts = this.posts.slice().sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
      } else if (this.sortBy === 'newest') {
        this.sortedPosts = this.posts.slice().sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
      }
    },
    searchPosts() {
      if (this.searchQuery.trim() === '') {
        this.sortedPosts = this.posts;
      } else {
        this.sortedPosts = this.posts.filter(post =>
          post.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          post.author_name.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }
    },
    formatTimeAgo(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diffInSeconds = Math.floor((now - date) / 1000);
      
      if (diffInSeconds < 60) {
        return 'just now';
      }
      
      const diffInMinutes = Math.floor(diffInSeconds / 60);
      if (diffInMinutes < 60) {
        return `${diffInMinutes} minute${diffInMinutes !== 1 ? 's' : ''} ago`;
      }
      
      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) {
        return `${diffInHours} hour${diffInHours !== 1 ? 's' : ''} ago`;
      }
      
      const diffInDays = Math.floor(diffInHours / 24);
      if (diffInDays < 7) {
        return `${diffInDays} day${diffInDays !== 1 ? 's' : ''} ago`;
      }
      
      const diffInWeeks = Math.floor(diffInDays / 7);
      if (diffInWeeks < 4) {
        return `${diffInWeeks} week${diffInWeeks !== 1 ? 's' : ''} ago`;
      }
      
      const diffInMonths = Math.floor(diffInDays / 30);
      if (diffInMonths < 12) {
        return `${diffInMonths} month${diffInMonths !== 1 ? 's' : ''} ago`;
      }
      
      const diffInYears = Math.floor(diffInDays / 365);
      return `${diffInYears} year${diffInYears !== 1 ? 's' : ''} ago`;
    },
    async fetchProfile() {
      try {
        const response = await axios.get('/api/getUserProfile', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('authToken')}`
          }
        });
        return response.data.userData;
      } catch (error) {
        console.error('Error fetching profile:', error);
        return null;
      }
    },
    async navigateToPost(postId) {
      this.$router.push({ name: 'PostDetails', params: { postId } });
    },
    async createNewPost() {
      this.isLoading = true;
      try {
        const profileData = await this.fetchProfile();
        if (profileData) {
          const response = await axios.post('/api/forum-posts', {
            title: this.newPostTitle,
            content: this.newPostContent,
            autors: profileData.id
          }, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('authToken')}`
            }
          });
          if (response.data && response.data.status) {
            this.showSuccessMessage('Post created successfully!');
            this.newPostTitle = '';
            this.newPostContent = '';
            this.showModal = false;
            this.getForumPosts();
          } else {
            this.showErrorMessage('Failed to create post. Please try again.');
          }
        } else {
          this.showErrorMessage('Unable to verify your profile. Please try logging in again.');
        }
      } catch (error) {
        console.error('Error creating post:', error);
        this.showErrorMessage('An error occurred while creating your post. Please try again later.');
      } finally {
        this.isLoading = false;
      }
    },
    
    showErrorMessage(message) {
      this.errorMessage = message;
      this.showError = true;
      setTimeout(() => {
        this.showError = false;
      }, 5000);
    },
    
    showSuccessMessage(message) {
      console.log(message);
    },
    
    truncateContent(content) {
      if (!content) return '';
      return content.length > 100 ? content.substring(0, 100) + '...' : content;
    },
    
    getInitials(name) {
      if (!name) return '';
      return name
        .split(' ')
        .map(word => word.charAt(0))
        .join('')
        .toUpperCase();
    }
  },
  mounted() {
    this.getForumPosts();
  }
};
</script>

<style scoped>
.forum-container {
  padding-top: 80px;
  background-color: var(--color-background);
  min-height: 100vh;
}

.content-wrapper {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--space-xl) var(--space-md);
  box-sizing: border-box;
}

.header-section {
  display: flex;
  flex-direction: column;
  margin-bottom: var(--space-xl);
}

.header-title {
  font-size: var(--font-size-xxl);
  font-weight: 700;
  color: var(--color-text-primary);
  margin-bottom: var(--space-md);
  text-align: left;
}

.forum-actions {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-md);
  align-items: center;
  justify-content: space-between;
  margin-top: var(--space-md);
}

.search-and-sort {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  flex-grow: 1;
  max-width: 600px;
}

.create-post-button {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  padding: var(--space-sm) var(--space-md);
  font-size: var(--font-size-md);
  font-weight: 600;
  border: none;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  transition: all 0.2s ease;
}

.create-post-button .icon {
  font-size: var(--font-size-lg);
  line-height: 1;
}

.search-bar {
  position: relative;
  flex-grow: 1;
}

.search-input {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  padding-right: 40px;
  font-size: var(--font-size-md);
  background-color: var(--color-surface);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: var(--border-radius-md);
  color: var(--color-text-primary);
}

.search-icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--color-text-muted);
}

.sort-controls {
  display: flex;
  align-items: center;
  min-width: 140px;
}

.sort-dropdown {
  padding: var(--space-sm) var(--space-md);
  font-size: var(--font-size-md);
  background-color: var(--color-surface);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: var(--border-radius-md);
  color: var(--color-text-primary);
  cursor: pointer;
}

.posts-container {
  padding: var(--space-md);
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
}

.post-list {
  padding: 0;
  list-style-type: none;
  width: 100%;
  margin: 0;
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-md);
}

.post-card {
  background: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-md);
  padding: var(--space-lg);
  cursor: pointer;
  text-align: left;
  color: var(--color-text-primary);
  transition: all 0.2s ease-in-out;
  border: 1px solid rgba(255, 255, 255, 0.1);
  position: relative;
  overflow: hidden;
}

.post-card::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background: var(--color-primary);
  opacity: 0.7;
}

.post-card:hover {
  transform: translateY(-3px);
  box-shadow: var(--shadow-lg);
  border-color: rgba(255, 255, 255, 0.2);
}

.post-content {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
}

.post-title {
  font-size: var(--font-size-lg);
  font-weight: 600;
  margin: 0 0 var(--space-xs) 0;
  color: var(--color-text-primary);
  line-height: 1.3;
}

.post-excerpt {
  color: var(--color-text-secondary);
  font-size: var(--font-size-md);
  margin: 0 0 var(--space-md) 0;
  line-height: 1.5;
}

.post-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: var(--space-sm);
  padding-top: var(--space-sm);
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.post-author {
  display: flex;
  align-items: center;
  gap: var(--space-xs);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
}

.author-avatar {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  background-color: var(--color-primary-dark);
  border-radius: 50%;
  font-size: var(--font-size-sm);
  font-weight: 600;
  color: var(--color-text-primary);
  text-overflow: ellipsis;
  white-space: nowrap;
}

.post-date {
  text-align: right;
}


.modal-overlay {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(4px);
}

.modal-content {
  background-color: var(--color-surface);
  margin: auto;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-xl);
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--space-lg);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-title {
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--color-text-primary);
  margin: 0;
}

.modal-close {
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  font-size: 24px;
  cursor: pointer;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background-color: rgba(255, 255, 255, 0.1);
  color: var(--color-text-primary);
}

.post-form {
  padding: var(--space-lg);
}

.form-control {
  margin-bottom: var(--space-md);
}

.form-control label {
  display: block;
  margin-bottom: var(--space-xs);
  font-size: var(--font-size-md);
  color: var(--color-text-secondary);
}

.form-input, .form-textarea {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  background-color: var(--color-background);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: var(--border-radius-md);
  color: var(--color-text-primary);
  font-size: var(--font-size-md);
  transition: border-color 0.2s ease;
}

.form-input:focus, .form-textarea:focus {
  outline: none;
  border-color: var(--color-primary);
}

.form-textarea {
  resize: vertical;
  min-height: 120px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: var(--space-md);
  margin-top: var(--space-lg);
}
</style>