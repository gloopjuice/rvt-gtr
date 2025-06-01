<template>
  <div class="forum-container">
    <div id="post-details-content" class="post-content">
      <div v-if="post" class="post-wrapper">
        <a href="/forums_hub" class="back-button">Atgriezties uz forumu</a>

        <div class="post-header">
          <h1 class="post-title">{{ post.title }}</h1>
          <div class="post-meta">
            <div class="author-info">
              <div class="profile-picture"></div>
              <p class="author-name">{{ post.authorProfile.name }}</p>
            </div>
            <p class="post-date">{{ formatDate(post.created_at) }}</p>
          </div>
        </div>

        <div class="post-body">
          <p class="post-description">{{ post.content }}</p>
        </div>

        <div class="post-actions" v-if="isAuthorOrAdmin">
          <button aria-label="edit post" class="action-button edit-button" @click="editPost">Rediģēt ierakstu</button>
          <button aria-label="delete post" class="action-button delete-button" @click="deletePost">Izdzēst ierakstu</button>
        </div>

        <div class="comments-section">
          <h2 class="section-title">Komentāri</h2>
          
          <div class="comment-form">
            <div class="comment-input" @click="showModal = true">
              <p>Atstāj komentāru!</p>
              <div class="input-line"></div>
            </div>
            <button class="post-comment-button" @click="showModal = true">Izveidot</button>
          </div>

          
          <div v-if="comments && comments.length > 0" class="comments-list">
            <div v-for="(comment, index) in comments" :key="index" class="comment-item">
              <div class="comment-header">
                <div class="profile-picture commenter-picture"></div>
                <p class="commenter-name">{{ comment.author ? comment.author.name : 'Unknown' }}</p>
              </div>
              <p class="comment-content">{{ comment.content }}</p>
              <div class="comment-footer">
                <p class="comment-date">{{ formatDate(comment.created_at) }}</p>
                <div class="comment-actions" v-if="canEditComment(comment) || canDeleteComment(comment)">
                  <button v-if="canEditComment(comment)" @click="openEditCommentModal(comment.id)" class="action-button edit-button">Rediģēt</button>
                  <button v-if="canDeleteComment(comment)" @click="deleteComment(comment.id)" class="action-button delete-button">Izdzēst</button>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="no-comments">
            <p>Vēl nav komentāru. Atstāj kaut ko pirmais!</p>
          </div>
        </div>
      </div>
      <div v-else class="loading">
        <p>Lādē ierakstu...</p>
      </div>
    </div>


    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal">
        <span class="close" @click="showModal = false">&times;</span>
        <h3>Atstāj komentāru</h3>
        <form @submit.prevent="addComment">
          <div>
            <textarea id="commentContent" v-model="commentContent" placeholder="Ievadi savu komentāru" class="modal-textarea" required></textarea>
          </div>
          <button type="submit" class="post-comment-button">Izveidot</button>
        </form>
      </div>
    </div>


    <div v-if="showEditModal" class="modal-overlay" @click.self="showEditModal = false">
      <div class="modal">
        <span class="close" @click="showEditModal = false">&times;</span>
        <h3>Rediģē ierakstu</h3>
        <form @submit.prevent="saveEditedPost">
          <div>
            <label for="editedTitle">Virsraksts:</label>
            <input type="text" id="editedTitle" v-model="editedTitle" placeholder="Ievadi virsrakstu" class="modal-input" required>
          </div>
          <div>
            <label for="editedContent">Apakšraksts:</label>
            <textarea id="editedContent" v-model="editedContent" placeholder="Ievadi apakšrakstu" class="modal-textarea" required></textarea>
          </div>
          <button type="submit" class="post-comment-button">Saglabāt</button>
        </form>
      </div>
    </div>

    <div v-if="showEditCommentModal" class="modal-overlay" @click.self="showEditCommentModal = false">
      <div class="modal">
        <span class="close" @click="showEditCommentModal = false">&times;</span>
        <h3>Edit Comment</h3>
        <form @submit.prevent="updateComment">
          <div>
            <textarea id="editedCommentContent" v-model="editedCommentContent" placeholder="Edit your comment" class="modal-textarea" required></textarea>
          </div>
          <button type="submit" class="post-comment-button">Atjaunot</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      post: null,
      showModal: false,
      commentContent: '',
      comments: [],
      showEditModal: false,
      editedTitle: '',
      editedContent: '',
      currentUser: null,
      showEditCommentModal: false,
      editingCommentId: null,
      editedCommentContent: '',
      loading: true,
      error: null
    };
  },
  
  computed: {
    isAuthorOrAdmin() {
      return this.currentUser && this.post && 
        (this.currentUser.id === this.post.author_id || this.currentUser.role_id === 1);
    }
  },
  
  methods: {
    formatDate(dateString) {
      if (!dateString) return 'Unknown date';
      return dateString.slice(0, 10);
    },
    
    canEditComment(comment) {
      return this.currentUser && 
        (comment.author_id === this.currentUser.id || this.currentUser.role_id === 1);
    },
        
    canDeleteComment(comment) {
      return this.currentUser && 
        (comment.author_id === this.currentUser.id || this.currentUser.role_id === 1);
    },
    
    async getPost(id) {
      const token = localStorage.getItem('authToken');
      console.log(`Fetching post with ID: ${id}`);
      try {
        const response = await axios.get(`/api/forum-posts/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        console.log('Post data:', response.data);
        const postData = response.data.data;
        
        const authorProfile = await this.fetchProfile(postData.author_id);
        postData.authorProfile = authorProfile;
        
        this.post = postData;
        this.editedTitle = postData.title;
        this.editedContent = postData.content;
        
        await this.fetchComments(id);
        this.currentUser = await this.fetchCurrentUser();
        this.loading = false;
      } catch (error) {
        console.error('Error fetching post:', error);
        this.error = 'Failed to load post data';
        this.loading = false;
      }
    },
    
    async fetchProfile(authorId) {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get(`/api/getUserProfile`, {
          headers: {
            Authorization: `Bearer ${token}`
          },
          params: { id: authorId }
        });
        return response.data.userData;
      } catch (error) {
        console.error('Error fetching profile:', error);
        return { name: 'Unknown Author' };
      }
    },
    
    async fetchComments(postId) {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get('/api/comments', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        if (response.data && response.data.comments) {

          this.comments = response.data.comments.filter(comment => 
            comment.post_id == postId
          );
          console.log('Filtered comments:', this.comments);
        } else {
          console.error('Comments data is missing or malformed', response.data);
          this.comments = [];
        }
      } catch (error) {
        console.error('Error fetching comments:', error);
        this.comments = [];
      }
    },
    
    async fetchCurrentUser() {
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.get('/api/profile', {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        return response.data.data;
      } catch (error) {
        console.error('Error fetching current user:', error);
        return null;
      }
    },
    
    async addComment() {
      const token = localStorage.getItem('authToken');
      const postId = this.$route.params.postId;
      
      try {
        const response = await axios.post('/api/comments', {
          post_id: postId,
          author_id: this.currentUser.id,
          content: this.commentContent
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        if (response.data && response.data.status) {
          alert('Comment added successfully');
          this.commentContent = '';
          this.showModal = false;
          await this.fetchComments(postId);
        } else {
          alert('Error adding comment');
        }
      } catch (error) {
        console.error('Error adding comment:', error);
        alert('Error adding comment: ' + (error.response?.data?.message || error.message));
      }
    },
    
    async deleteComment(commentId) {
      if (!confirm('Vai tiešām vēlies dzēst šo komentāru?')) return;
      
      const token = localStorage.getItem('authToken');
      try {
        const response = await axios.delete(`/api/comments/${commentId}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        if (response.data && response.data.status) {
          alert('Comment deleted successfully');
          await this.fetchComments(this.post.id);
        } else {
          alert('Error deleting comment');
        }
      } catch (error) {
        console.error('Error deleting comment:', error);
        alert('Error deleting comment: ' + (error.response?.data?.message || error.message));
      }
    },
    
    editPost() {
      this.editedTitle = this.post.title;
      this.editedContent = this.post.content;
      this.showEditModal = true;
    },
    
    async saveEditedPost() {
      const token = localStorage.getItem('authToken');
      const postId = this.post.id;
      
      try {
        const response = await axios.put(`/api/forum-posts/${postId}`, {
          title: this.editedTitle,
          content: this.editedContent
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        if (response.data && response.data.status) {
          alert('Post edited successfully');
          this.showEditModal = false;
          await this.getPost(postId);
        } else {
          alert('Error editing post');
        }
      } catch (error) {
        console.error('Error editing post:', error);
        alert('Error editing post: ' + (error.response?.data?.message || error.message));
      }
    },
    
    async deletePost() {
      if (!confirm('Vai tiešām vēlies dzēst šo ierakstu?')) return;
      
      const token = localStorage.getItem('authToken');
      const postId = this.post.id;
      
      try {
        const response = await axios.delete(`/api/forum-posts/${postId}`, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        if (response.data && response.data.status) {
          alert('Post deleted successfully');
          this.$router.push('/forums_hub');
        } else {
          alert('Error deleting post');
        }
      } catch (error) {
        console.error('Error deleting post:', error);
        alert('Error deleting post: ' + (error.response?.data?.message || error.message));
      }
    },
    
    openEditCommentModal(commentId) {
      this.editingCommentId = commentId;
      const comment = this.comments.find(c => c.id === commentId);
      
      if (comment) {
        this.editedCommentContent = comment.content;
        this.showEditCommentModal = true;
      }
    },
    
    async updateComment() {
      const token = localStorage.getItem('authToken');
      const commentId = this.editingCommentId;
      
      try {
        const response = await axios.put(`/api/comments/${commentId}`, {
          content: this.editedCommentContent
        }, {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
        
        if (response.data && response.data.status) {
          alert('Comment updated successfully');
          this.showEditCommentModal = false;
          await this.fetchComments(this.post.id);
        } else {
          alert('Error updating comment');
        }
      } catch (error) {
        console.error('Error updating comment:', error);
        alert('Error updating comment: ' + (error.response?.data?.message || error.message));
      }
    }
  },
  
  mounted() {
    const postId = this.$route.params.postId;
    this.getPost(postId);
  },
  
  components: {
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap');

.forum-container {
  width: 100%;
  min-height: 100vh;
  padding-top: 80px;
  background-color: var(--color-background);
  color: var(--color-text-primary);
  position: relative;
}

.post-content {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--space-xl) var(--space-md);
}

.post-wrapper {
  position: relative;
  width: 100%;
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--space-xl);
  overflow: hidden;
}

.back-button {
  display: inline-flex;
  align-items: center;
  font-size: var(--font-size-md);
  color: var(--color-text-secondary);
  text-decoration: none;
  margin-bottom: var(--space-lg);
  font-weight: 500;
  transition: color 0.2s ease;
  gap: var(--space-xs);
}

.back-button:before {
  content: '←';
  margin-right: var(--space-xs);
}

.back-button:hover {
  color: var(--color-primary);
}

.post-header {
  margin-bottom: var(--space-xl);
  position: relative;
}

.post-header:after {
  content: '';
  position: absolute;
  left: 0;
  bottom: -1px;
  width: 100%;
  height: 1px;
  background: linear-gradient(to right, var(--color-primary), transparent);
}

.post-title {
  font-size: var(--font-size-xxxl);
  font-weight: 700;
  margin-bottom: var(--space-md);
  line-height: 1.2;
  color: var(--color-text-primary);
}

.post-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--space-lg);
  padding-bottom: var(--space-md);
}

.author-info {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
}

.profile-picture {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background-color: var(--color-primary-dark);
  border-radius: 50%;
  font-size: var(--font-size-md);
  font-weight: 600;
  color: var(--color-text-primary);
}

.commenter-picture {
  width: 36px;
  height: 36px;
  font-size: var(--font-size-sm);
}

.author-name, .commenter-name {
  font-size: var(--font-size-lg);
  font-weight: 500;
  color: var(--color-text-secondary);
}
.commenter-name{
  margin-top: 10px;
  margin-left: 30px;
}

.post-date, .comment-date {
  font-size: var(--font-size-sm);
  color: var(--color-text-muted);
}

.post-body {
  margin-bottom: var(--space-xl);
  padding-bottom: var(--space-lg);
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.post-description {
  font-size: var(--font-size-lg);
  line-height: 1.6;
  margin-bottom: var(--space-lg);
  color: var(--color-text-secondary);
  white-space: pre-line;
}

.post-actions {
  display: flex;
  gap: var(--space-md);
  margin-bottom: var(--space-xl);
}

.action-button {
  padding: var(--space-sm) var(--space-md);
  font-size: var(--font-size-md);
  border: none;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  color: white;
  font-weight: 500;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: var(--space-xs);
}

.action-button:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.edit-button {
  background-color: var(--color-primary-dark);
}

.edit-button:hover {
  background-color: var(--color-primary);
}

.delete-button {
  background-color: var(--color-danger, #e53935);
}

.delete-button:hover {
  background-color: #f44336;
}

.section-title {
  font-size: var(--font-size-xl);
  font-weight: 600;
  margin-bottom: var(--space-lg);
  color: var(--color-text-primary);
  position: relative;
  padding-bottom: var(--space-sm);
}

.section-title:after {
  content: '';
  position: absolute;
  left: 0;
  bottom: 0;
  width: 60px;
  height: 3px;
  background-color: var(--color-primary);
  border-radius: 3px;
}

.comment-form {
  display: flex;
  align-items: center;
  margin-bottom: var(--space-xl);
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: var(--border-radius-lg);
  padding: var(--space-md);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.comment-input {
  flex-grow: 1;
  margin-right: var(--space-md);
  cursor: pointer;
  padding: var(--space-sm) var(--space-md);
  border-radius: var(--border-radius-md);
  background-color: rgba(255, 255, 255, 0.05);
  transition: background-color 0.2s ease;
}

.comment-input:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.comment-input p {
  font-size: var(--font-size-md);
  margin-bottom: var(--space-xs);
  color: var(--color-text-secondary);
}

.input-line {
  width: 100%;
  height: 2px;
  background: linear-gradient(to right, var(--color-primary-light), transparent);
  margin-top: var(--space-xs);
}

.post-comment-button {
  padding: var(--space-sm) var(--space-md);
  font-size: var(--font-size-md);
  background-color: var(--color-primary);
  border: none;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  color: white;
  font-weight: 500;
  transition: all 0.2s ease;
}

.post-comment-button:hover {
  background-color: var(--color-primary-light);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.comments-list {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.comment-item {
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 10px;
}

.comment-header {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.comment-content {
  font-size: 20px;
  line-height: 1.5;
  margin-bottom: 15px;
  padding-left: 65px;
}

.comment-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-left: 65px;
}

.comment-actions {
  display: flex;
  gap: 10px;
}

.comment-actions .action-button {
  padding: 5px 15px;
  font-size: 14px;
}

.no-comments {
  text-align: center;
  padding: 30px;
  font-size: 20px;
  color: #BBBBBB;
}

.loading {
  text-align: center;
  padding: 50px;
  font-size: 24px;
}

.author-name
  {
    padding-left: 10px;
  }
  
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 100;
}

.modal {
  background-color: #444444;
  padding: 30px;
  border-radius: 8px;
  width: 80%;
  max-width: 600px;
  position: relative;
  color: white;
}

.modal h3 {
  font-size: 24px;
  margin-bottom: 20px;
  font-weight: 400;
}

.close {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 28px;
  cursor: pointer;
  color: #BBBBBB;
}

.modal-textarea, .modal-input {
  width: 100%;
  background: #555555;
  color: white;
  border: 1px solid #666666;
  padding: 15px;
  margin-bottom: 20px;
  border-radius: 4px;
}

.modal-textarea {
  min-height: 150px;
  resize: vertical;
}

.modal .post-comment-button {
  margin-left: auto;
  display: block;
}

@media (max-width: 768px) {
  .post-title {
    font-size: 36px;
  }
  
  .post-description, .author-name, .commenter-name, .comment-input p {
    font-size: 18px;
  }

  .comment-content {
    font-size: 16px;
  }
  
  .profile-picture {
    width: 40px;
    height: 40px;
  }
  
  .commenter-picture {
    width: 30px;
    height: 30px;
  }
  
  .post-actions, .comment-actions {
    flex-direction: column;
    gap: 10px;
  }
  
  .comment-footer {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style>