<template>
  <div id="management-box">
    <Header />
    <div class="management-section">
      <h2 class="page-title">Lietotāju pārvaldība</h2>
      
      <div class="table-container">
        <table class="users-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Vārds</th>
              <th>Lietotājvārds</th>
              <th>E-pasts</th>
              <th>Bio</th>
              <th></th>            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>{{ user.id }}</td>
              <td>{{ user.name }}</td>
              <td>{{ user.username }}</td>
              <td>{{ user.email }}</td>
              <td>{{ user.bio ? user.bio.substring(0, 10) + (user.bio.length > 10 ? '...' : '') : 'Nav bio' }}</td>
              <td>
                <div class="action-buttons">
                  <button class="btn btn-primary" @click="editUser(user.id)" aria-label="edit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Rediģēt
                  </button>
                  <button class="btn btn-danger" @click="confirmDelete(user.id)" aria-label="delete">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M3 6H5H21M19 6V20C19 21.1046 18.1046 22 17 22H7C5.89543 22 5 21.1046 5 20V6M8 6V4C8 2.89543 8.89543 2 10 2H14C15.1046 2 16 2.89543 16 4V6M10 11V17M14 11V17"/>
                    </svg>
                    Dzēst
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <button class="back-button" @click="goBack" aria-label="atpakaļ">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19L5 12L12 5"/>
      </svg>
      Atpakaļ uz profilu
    </button>

    <div v-if="showEditModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Rediģēt lietotāju</h3>
        <div class="edit-form-group">
          <label for="name">Vārds:</label>
          <input type="text" id="name" v-model="editFormData.name" required>
        </div>
        <div class="edit-form-group">
          <label for="username">Lietotājvārds:</label>
          <input type="text" id="username" v-model="editFormData.username" required>
        </div>
        <div class="edit-form-group">
          <label for="email">E-pasts:</label>
          <input type="email" id="email" v-model="editFormData.email" required>
        </div>
        <div class="edit-form-group">
          <label for="bio">Bio:</label>
          <textarea id="bio" v-model="editFormData.bio"></textarea>
        </div>
        <div class="modal-buttons">
          <button class="cancel-btn" @click="cancelEdit">Atcelt</button>
          <button class="confirm-btn" @click="saveUser">Saglabāt</button>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="modal-overlay">
      <div class="modal-content">
        <h3>Konfirmēt dzēšanu</h3>
        <p>Vai tiešām vēlies dzēst šo lietotāju? Izmaiņas ir neatgriezeniskas!</p>
        <div class="modal-buttons">
          <button class="cancel-btn" @click="showDeleteModal = false">Atcelt</button>
          <button class="confirm-btn" @click="deleteUser()">Dzēst</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Header from '../components/Header.vue'
import axios from 'axios'

export default {
  name: "UserManagementView",
  data() {
    return {
      users: [],
      showDeleteModal: false,
      userToDelete: null,
      showEditModal: false,
      editingUser: null,
      editFormData: {
        name: '',
        username: '',
        email: '',
        bio: ''
      }
    };
  },
  mounted() {
    const authToken = localStorage.getItem('authToken');
    if (authToken) {
      this.fetchAllUsers();
    } else {
      this.$router.push('/login');
    }
  },
  methods: {
    fetchAllUsers() {
      axios.get('/api/users', {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      })
      .then(response => {
        this.users = response.data.users;
      })
      .catch(error => {
        console.error('Error fetching users:', error);
      });
    },
    editUser(userId) {
      const user = this.users.find(u => u.id === userId);
      if (user) {
        this.editFormData = {
          name: user.name,
          username: user.username,
          email: user.email,
          bio: user.bio || ''
        };
        this.editingUser = userId;
        this.showEditModal = true;
      }
    },
    saveUser() {
      axios.put(`/api/users/${this.editingUser}`, this.editFormData, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      })
      .then(response => {
        this.showEditModal = false;
        this.editingUser = null;
        this.fetchAllUsers(); 
        alert('Lietotāja izmaiņas veiksmīgi saglabātas');
      })
      .catch(error => {
        console.error('Error updating user:', error);
        alert('Kļūda lietotāja izmaiņu saglabāšanā');
      });
    },
    cancelEdit() {
      this.showEditModal = false;
      this.editingUser = null;
      this.editFormData = {
        name: '',
        username: '',
        email: '',
        bio: ''
      };
    },
    confirmDelete(userId) {
      if (confirm('Vai tiešām vēlies dzēst šo lietotāju? Izmaiņas ir neatgriezeniskas!')) {
        this.deleteUser(userId);
      }
    },
    deleteUser(userId) {
      axios.delete(`/api/users/${userId}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('authToken')}`
        }
      })
      .then(response => {
        this.fetchAllUsers(); 
        alert('Lietotājs veiksmīgi dzēsts');
      })
      .catch(error => {
        console.error('Error deleting user:', error);
        alert('Kļūda lietotāja dzēšanā');
      });
    },
    goBack() {
      this.$router.push('/profile');
    }
  },
  components: {
    Header,
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Kokoro&display=swap');

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

#management-box {
  height: 100vh;
  background-color: #373737;
  font-family: 'Kokoro', sans-serif;
  display: flex;
  flex-direction: column;
  padding-top: 5rem;
  position: relative;
  padding-left: 2rem;
  padding-right: 2rem;
}

.management-section {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-bottom: 2.5rem;
  width: 100%;
}

.page-title {
  font-size: 3rem;
  color: #FFFFFF;
  margin-bottom: 2rem;
}

.table-container {
  width: 100%;
  overflow-x: auto;
  margin-bottom: 2rem;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
  color: #FFFFFF;
}

.users-table th,
.users-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #555555;
}

.users-table th {
  font-size: 1.2rem;
  background-color: #444444;
}

.users-table td {
  font-size: 1.1rem;
}

.action-buttons {
  display: flex;
  gap: 0.75rem;
}

.btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  font-size: 0.9rem;
  font-family: 'Kokoro', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 100px;
}

.btn-primary {
  background-color: #4CAF50;
  color: white;
  border: none;
}

.btn-primary:hover {
  background-color: #45a049;
}

.btn-danger {
  background-color: #ff4d4d;
  color: white;
  border: none;
}

.btn-danger:hover {
  background-color: #cc0000;
}

.btn svg {
  width: 1rem;
  height: 1rem;
  stroke: currentColor;
}

.back-button {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  background: transparent;
  border: 2px solid #4CAF50;
  border-radius: 0.75rem;
  color: #4CAF50;
  font-size: 0.9rem;
  font-family: 'Kokoro', sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 150px;
}

.back-button:hover {
  background-color: #4CAF50;
  color: white;
}

.back-button svg {
  width: 1rem;
  height: 1rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: #373737;
  padding: 2rem;
  border-radius: 0.75rem;
  max-width: 400px;
  width: 90%;
  color: white;
}

.modal-buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1rem;
}

.modal-buttons button {
  padding: 0.75rem 1.5rem;
  border-radius: 0.375rem;
  font-size: 1rem;
  font-family: 'Kokoro', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
  min-width: 100px;
  border: none;
}

.cancel-btn {
  background-color: #888888;
  color: white;
}

.cancel-btn:hover {
  background-color: #666666;
}

.confirm-btn {
  background-color: #4CAF50;
  color: white;
}

.confirm-btn:hover {
  background-color: #45a049;
}

.edit-form-group {
  margin-bottom: 1.5rem;
}

.edit-form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-size: 1rem;
  color: #888888;
  font-weight: 500;
}

.edit-form-group input,
.edit-form-group textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #444444;
  border-radius: 0.375rem;
  background-color: #444444;
  color: white;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.edit-form-group textarea {
  height: 120px;
  resize: vertical;
  min-height: 100px;
  max-height: 200px;
}

.edit-form-group input:focus,
.edit-form-group textarea:focus {
  outline: none;
  border-color: #4CAF50;
  background-color: #444444;
}

.edit-form-group input::placeholder,
.edit-form-group textarea::placeholder {
  color: #888888;
}

@media screen and (max-width: 680px) {
  #management-box {
    padding-left: 1rem;
    padding-right: 1rem;
    padding-top: 4rem;
  }

  .page-title {
    font-size: 2.5rem;
  }

  .users-table th {
    font-size: 1rem;
  }

  .users-table td {
    font-size: 0.9rem;
  }

  .delete-btn {
    font-size: 0.8rem;
    padding: 0.4rem 0.6rem;
  }

  .delete-btn svg {
    width: 1rem;
    height: 1rem;
  }

  .back-button {
    width: 10rem;
    height: 2.5rem;
    font-size: 0.9rem;
    bottom: 1.5rem;
    left: 50%;
    transform: translateX(-50%);
  }
}
</style>