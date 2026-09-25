<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import Input from '@/Components/ui/Input.vue'
import Button from '@/Components/ui/Button.vue'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const submit = () => form.post('/login')

defineOptions({ layout: AuthLayout })
</script>

<template>
  <div class="login-container">
    <div class="login-wrapper">
      <!-- Left Panel: Branding & Information -->
      <div class="left-panel">
        <div class="left-panel-content">
          <!-- Logo -->
          <div class="logo-section">
            <img 
              src="/images/Kedebah Logo.png" 
              alt="KEDEBAH ERP" 
              class="brand-logo"
            />
          </div>
          
          <!-- Main Heading -->
          <h1 class="main-heading">Welcome to Project Tracker</h1>
          
          <!-- Subheading -->
          <p class="subheading">
            Streamline your project management with real-time collaboration, 
            task tracking, and team coordination all in one place.
          </p>
          
          <!-- Illustration Placeholder -->
          <div class="illustration-space">
            <!-- Space reserved for illustration image -->
            <img 
              src="/images/media-86.png" 
              alt="Project Management Illustration" 
              class="illustration-image"
            />
          </div>
          
          <!-- Features Section -->
          <div class="features-section">
            <!-- Feature 1 -->
            <div class="feature-item">
              <h3 class="feature-heading">Secure & Reliable</h3>
              <p class="feature-text">
                <span class="bullet-marker">•</span> Enterprise-grade security
              </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-item">
              <h3 class="feature-heading">Real-time Updates</h3>
              <p class="feature-text">
                <span class="bullet-marker">•</span> Stay synced instantly
              </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-item">
              <h3 class="feature-heading">Powerful Dashboard</h3>
              <p class="feature-text">
                <span class="bullet-marker">•</span> Track progress with analytics
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel: Login Form -->
      <div class="right-panel">
        <div class="form-container">
          <!-- Header -->
          <div class="form-header">
            <h2 class="form-title">Sign In</h2>
            <p class="form-subtitle">
              Enter your credentials to access your account
            </p>
          </div>

          <!-- Login Form -->
          <form class="login-form" @submit.prevent="submit">
            <!-- Email Input -->
            <Input
              v-model="form.email"
              type="email"
              label="Email Address"
              placeholder="you@example.com"
              icon="ri-mail-line"
              :error="form.errors.email"
              required
              autocomplete="email"
            />

            <!-- Password Input -->
            <Input
              v-model="form.password"
              type="password"
              label="Password"
              placeholder="Enter your password"
              icon="ri-lock-line"
              :error="form.errors.password"
              required
              autocomplete="current-password"
            />

            <!-- Remember Me & Forgot Password -->
            <div class="form-options">
              <label class="remember-me">
                <input
                  v-model="form.remember"
                  type="checkbox"
                  class="checkbox-input"
                />
                <span class="checkbox-label">Remember me</span>
              </label>

              <Link
                href="/forgot-password"
                class="forgot-link"
              >
                Forgot password?
              </Link>
            </div>

            <!-- Submit Button -->
            <Button
              type="submit"
              variant="primary"
              size="lg"
              full-width
              :loading="form.processing"
              :disabled="form.processing"
            >
              <i class="ri-login-box-line mr-2"></i>
              {{ form.processing ? 'Signing in...' : 'Sign In' }}
            </Button>
          </form>

          <!-- Divider -->
          <div class="divider">
            <div class="divider-line"></div>
            <div class="divider-text">
              <span class="divider-content">
                New to our platform?
              </span>
            </div>
          </div>

          <!-- Register Link -->
          <div class="register-section">
            <Link
              href="/register"
              class="register-link"
            >
              <i class="ri-user-add-line mr-2"></i>
              Create an Account
            </Link>
          </div>

          <!-- Additional Info -->
          <div class="terms-section">
            <p class="terms-text">
              By signing in, you agree to our 
              <a href="#" class="terms-link">Terms</a>
              and 
              <a href="#" class="terms-link">Privacy Policy</a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="login-footer">
      <p class="footer-text">© 2026 KEDEBAH ERP. All rights reserved.</p>
    </footer>
  </div>
</template>

<style scoped>
/* ===========================
   Two-Panel Login Layout
   =========================== */

/* Main Container */
.login-container {
  width: 100%;
  min-height: 100vh;
  height: 100%;
  display: flex;
  flex-direction: column;
  background: transparent;
  margin: 0;
  padding: 0;
}

/* Wrapper for Two Panels */
.login-wrapper {
  flex: 1;
  display: flex;
  flex-direction: row;
  width: 100%;
  min-height: calc(100vh - 60px); /* Account for footer height */
  margin: 0;
  padding: 0;
}

/* ===========================
   LEFT PANEL - Branding
   =========================== */
.left-panel {
  flex: 1;
  width: 50%;
  background: linear-gradient(135deg, #0f766e 0%, #047857 50%, #065f46 100%);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 6%;
  position: relative;
  overflow: hidden;
}

.left-panel::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
  pointer-events: none;
}

.left-panel-content {
  width: 100%;
  max-width: 700px;
  z-index: 1;
  position: relative;
}

/* Logo Section */
.logo-section {
  margin-bottom: 3rem;
}

.brand-logo {
  height: 48px;
  width: auto;
  filter: brightness(0) invert(1);
}

/* Main Heading - Serif Font */
.main-heading {
  font-family: Georgia, 'Times New Roman', Times, serif;
  font-size: 3rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  color: #ffffff;
  letter-spacing: -0.02em;
}

/* Subheading - Sans-serif */
.subheading {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 1.125rem;
  line-height: 1.7;
  color: #d1fae5;
  margin-bottom: 3rem;
  font-weight: 400;
}

/* Illustration Space */
.illustration-space {
  margin: 3rem 0;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.15;
}

.illustration-image {
  max-width: 100%;
  height: auto;
  object-fit: contain;
}

/* Features Section */
.features-section {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  margin-top: 2rem;
}

.feature-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

/* Feature Heading - Bold Sans-serif */
.feature-heading {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 1.25rem;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

/* Feature Text with Custom Bullet */
.feature-text {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 0.95rem;
  color: #d1fae5;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin: 0;
  padding-left: 0.25rem;
}

.bullet-marker {
  color: #6ee7b7;
  font-size: 1.5rem;
  line-height: 1;
}

/* ===========================
   RIGHT PANEL - Login Form
   =========================== */
.right-panel {
  flex: 1;
  width: 50%;
  background: #1f2937;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 6%;
}

.form-container {
  width: 100%;
  max-width: 520px;
}

/* Form Header */
.form-header {
  margin-bottom: 2.5rem;
  text-align: left;
}

.form-title {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  font-size: 2rem;
  font-weight: 700;
  color: #f9fafb;
  margin-bottom: 0.5rem;
}

.form-subtitle {
  font-size: 1rem;
  color: #d1d5db;
  font-weight: 400;
}

/* Login Form */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Form Options Row */
.form-options {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-input {
  width: 1rem;
  height: 1rem;
  border-radius: 0.25rem;
  border: 1px solid #d1d5db;
  color: #059669;
  cursor: pointer;
  transition: all 0.2s;
}

.checkbox-input:focus {
  outline: none;
  ring: 2px;
  ring-color: #059669;
  ring-offset: 0;
}

.checkbox-label {
  font-size: 0.875rem;
  color: #e5e7eb;
  user-select: none;
}

.forgot-link {
  font-size: 0.875rem;
  font-weight: 500;
  color: #059669;
  text-decoration: none;
  transition: color 0.2s;
}

.forgot-link:hover {
  color: #047857;
}

/* Divider */
.divider {
  position: relative;
  margin: 2rem 0;
}

.divider-line {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: center;
}

.divider-line::before {
  content: '';
  width: 100%;
  border-top: 1px solid #e5e7eb;
}

.divider-text {
  position: relative;
  display: flex;
  justify-center: center;
  text-align: center;
}

.divider-content {
  padding: 0 1rem;
  background: #1f2937;
  font-size: 0.875rem;
  color: #9ca3af;
}

/* Register Section */
.register-section {
  text-align: center;
}

.register-link {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 0.75rem 1.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  color: #e5e7eb;
  background: #374151;
  border-radius: 0.75rem;
  text-decoration: none;
  transition: background 0.2s;
}

.register-link:hover {
  background: #4b5563;
}

/* Terms Section */
.terms-section {
  text-align: center;
  margin-top: 1.5rem;
}

.terms-text {
  font-size: 0.75rem;
  color: #9ca3af;
  line-height: 1.6;
}

.terms-link {
  color: #059669;
  text-decoration: none;
}

.terms-link:hover {
  text-decoration: underline;
}

/* ===========================
   FOOTER - Full Width
   =========================== */
.login-footer {
  background: #111827;
  padding: 1.25rem 2rem;
  text-align: center;
  border-top: 1px solid #374151;
  width: 100%;
  flex-shrink: 0;
}

.footer-text {
  font-size: 0.875rem;
  color: #9ca3af;
  margin: 0;
  font-weight: 400;
}

/* ===========================
   RESPONSIVE DESIGN
   =========================== */

/* Tablet and Below */
@media (max-width: 1024px) {
  .login-wrapper {
    flex-direction: column;
  }

  .left-panel,
  .right-panel {
    min-width: 100%;
    padding: 3rem 2rem;
  }

  .main-heading {
    font-size: 2.5rem;
  }

  .illustration-space {
    min-height: 150px;
    margin: 2rem 0;
  }
}

/* Mobile */
@media (max-width: 640px) {
  .left-panel,
  .right-panel {
    padding: 2rem 1.5rem;
  }

  .logo-section {
    margin-bottom: 2rem;
  }

  .brand-logo {
    height: 40px;
  }

  .main-heading {
    font-size: 2rem;
  }

  .subheading {
    font-size: 1rem;
  }

  .form-title {
    font-size: 1.75rem;
  }

  .features-section {
    gap: 1.5rem;
  }

  .feature-heading {
    font-size: 1.125rem;
  }
}

/* ===========================
   ANIMATIONS
   =========================== */

/* Form Elements Animation */
.login-form > * {
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}

.login-form > *:nth-child(1) { animation-delay: 0.1s; }
.login-form > *:nth-child(2) { animation-delay: 0.2s; }
.login-form > *:nth-child(3) { animation-delay: 0.3s; }
.login-form > *:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Dark Mode Support (Optional) */
@media (prefers-color-scheme: dark) {
  .login-container {
    background: transparent;
  }

  .right-panel {
    background: #1f2937;
  }

  .form-title {
    color: #f9fafb;
  }

  .form-subtitle {
    color: #d1d5db;
  }

  .checkbox-label {
    color: #e5e7eb;
  }

  .divider-content {
    background: #1f2937;
  }

  .register-link {
    background: #374151;
    color: #e5e7eb;
  }

  .register-link:hover {
    background: #4b5563;
  }

  .login-footer {
    background: #111827;
    border-top-color: #374151;
  }
}
</style>