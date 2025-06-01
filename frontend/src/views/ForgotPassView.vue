<template>
  <div class="forgot-password-page">
    <div class="forgot-password-container card">
      <div class="forgot-password-header">
        <h1 class="logo-text">RVTGTR</h1>
        <h2 class="page-title">Forgot your password?</h2>
        <p class="subtitle">Enter your email address and we'll send you a link to reset your password</p>
      </div>
      
      <form @submit.prevent="handleSubmit" class="forgot-password-form">
        <div class="form-control">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            class="form-input" 
            placeholder="Enter your email address" 
            v-model="email" 
            required
          >
        </div>
        
        <div v-if="message" :class="['message', messageType]">
          {{ message }}
        </div>
        
        <div class="form-actions">
          <button 
            type="submit" 
            class="btn btn-primary" 
            :disabled="isLoading"
          >
            <span v-if="isLoading">Sending...</span>
            <span v-else>Send Reset Link</span>
          </button>
        </div>
      </form>
      
      <div class="forgot-password-footer">
        <p>Remember your password? <RouterLink to="/login" class="login-link">Sign in</RouterLink></p>
        <p>Don't have an account? <RouterLink to="/register" class="register-link">Create account</RouterLink></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const isLoading = ref(false)
const message = ref('')
const messageType = ref('')

const handleSubmit = async () => {
  if (!email.value) {
    setMessage('Please enter your email address', 'error')
    return
  }
  
  if (!email.value.includes('@') || !email.value.includes('.')) {
    setMessage('Please enter a valid email address', 'error')
    return
  }
  
  isLoading.value = true
  message.value = ''
  
  try {
    const baseUrl = 'http://127.0.0.1:8000'
    const url = `${baseUrl}/api/forgot-password`
    
    console.log('Sending password reset request to:', url)
    
    const response = await axios.post(url, {
      email: email.value
    })
    
    console.log('Password reset response:', response.data)
    
    setMessage('If an account exists with this email, a password reset link has been sent. Please check your inbox.', 'success')
    email.value = ''
  } catch (error) {
    console.error('Forgot password error:', error)
    
    if (error.response) {
      console.log('Error response:', error.response.data)
      
      if (error.response.status === 404) {
        setMessage('The password reset service is currently unavailable. Please try again later.', 'error')
      } else if (error.response.data && error.response.data.message) {
        setMessage(error.response.data.message, 'error')
      } else {
        setMessage('Failed to send reset link. Please try again later.', 'error')
      }
    } else {
      setMessage('Network error. Please check your connection and try again.', 'error')
    }
  } finally {
    isLoading.value = false
  }
}

const setMessage = (text, type) => {
  message.value = text
  messageType.value = type
  
  if (type === 'success') {
    setTimeout(() => {
      if (messageType.value === 'success') {
        message.value = ''
      }
    }, 5000)
  }
}
</script>
<style scoped>
.forgot-password-page {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: var(--space-xl) var(--space-md);
  background-color: var(--color-background);
}

.forgot-password-container {
  width: 100%;
  max-width: 450px;
  margin: 0 auto;
  background-color: var(--color-surface);
  border-radius: var(--border-radius-lg);
  box-shadow: var(--shadow-lg);
  padding: var(--space-xl);
}

.forgot-password-header {
  text-align: center;
  margin-bottom: var(--space-xl);
}

.logo-text {
  font-size: var(--font-size-xxxl);
  font-weight: 700;
  color: var(--color-primary);
  margin-bottom: var(--space-xs);
  letter-spacing: 1px;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.page-title {
  font-size: var(--font-size-xl);
  font-weight: 600;
  color: var(--color-text-primary);
  margin-bottom: var(--space-sm);
}

.subtitle {
  color: var(--color-text-secondary);
  font-size: var(--font-size-md);
  margin-bottom: var(--space-md);
}

.forgot-password-form {
  margin-bottom: var(--space-lg);
}

.form-control {
  margin-bottom: var(--space-md);
}

.form-control label {
  display: block;
  font-size: var(--font-size-md);
  margin-bottom: var(--space-xs);
  color: var(--color-text-secondary);
}

.form-input {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  font-size: var(--font-size-md);
  background-color: var(--color-background);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: var(--border-radius-md);
  color: var(--color-text-primary);
  transition: border-color 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary);
}

.message {
  padding: var(--space-sm);
  margin-bottom: var(--space-md);
  border-radius: var(--border-radius-sm);
  font-size: var(--font-size-sm);
}

.message.success {
  background-color: rgba(76, 175, 80, 0.1);
  border-left: 3px solid var(--color-success, #4caf50);
  color: var(--color-success, #4caf50);
}

.message.error {
  background-color: rgba(255, 87, 87, 0.1);
  border-left: 3px solid var(--color-danger, #ff5757);
  color: var(--color-danger, #ff5757);
}

.form-actions {
  margin-top: var(--space-lg);
}

.btn {
  width: 100%;
  padding: var(--space-sm) var(--space-md);
  font-size: var(--font-size-md);
  font-weight: 600;
  border: none;
  border-radius: var(--border-radius-md);
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-primary {
  background-color: var(--color-primary);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background-color: var(--color-primary-dark);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

.forgot-password-footer {
  text-align: center;
  margin-top: var(--space-lg);
  color: var(--color-text-secondary);
  font-size: var(--font-size-sm);
}

.forgot-password-footer p {
  margin-bottom: var(--space-xs);
}

.login-link, .register-link {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.login-link:hover, .register-link:hover {
  color: var(--color-primary-light);
  text-decoration: underline;
}

@media (max-width: 480px) {
  .forgot-password-container {
    padding: var(--space-lg) var(--space-md);
  }
  
  .logo-text {
    font-size: var(--font-size-xxl);
  }
  
  .page-title {
    font-size: var(--font-size-lg);
  }
}
</style>
