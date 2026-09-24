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
  <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <!-- Main Login Card Container -->
    <div class="w-full max-w-6xl bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden">
      <div class="flex flex-col lg:flex-row min-h-[600px]">
        
        <!-- Left Side: Welcome Message & Illustration -->
        <div class="lg:w-2/5 bg-gradient-to-br from-green-600 via-green-700 to-emerald-800 p-8 lg:p-12 flex flex-col justify-between relative overflow-hidden">
          <!-- Background Decoration -->
          <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
          <div class="absolute bottom-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -ml-48 -mb-48"></div>
          
          <!-- Illustration Image -->
          <div class="absolute inset-0 opacity-10">
            <img 
              src="/images/media-86.png" 
              alt="Project Management Illustration" 
              class="w-full h-full object-cover"
            />
          </div>
          
          <!-- Content -->
          <div class="relative z-10">
            <!-- Logo -->
            <div class="mb-8 lg:mb-12">
              <img 
                src="/images/Kedebah Logo.png" 
                alt="KEDEBAH ERP" 
                class="h-10 w-auto brightness-0 invert"
              />
            </div>
            
            <!-- Welcome Text -->
            <div class="text-white">
              <h1 class="text-3xl lg:text-4xl font-bold mb-4 leading-tight">
                Welcome to<br />Project Tracker
              </h1>
              <p class="text-green-50 text-base lg:text-lg leading-relaxed mb-6">
                Streamline your project management with real-time collaboration, 
                task tracking, and team coordination all in one place.
              </p>
              
              <!-- Features -->
              <div class="space-y-4">
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                    <i class="ri-shield-check-line text-xl"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold mb-1">Secure & Reliable</h3>
                    <p class="text-green-100 text-sm">Enterprise-grade security</p>
                  </div>
                </div>
                
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                    <i class="ri-time-line text-xl"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold mb-1">Real-time Updates</h3>
                    <p class="text-green-100 text-sm">Stay synced instantly</p>
                  </div>
                </div>
                
                <div class="flex items-start gap-3">
                  <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center">
                    <i class="ri-dashboard-line text-xl"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold mb-1">Powerful Dashboard</h3>
                    <p class="text-green-100 text-sm">Track progress with analytics</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Footer -->
          <div class="relative z-10 text-green-100 text-xs mt-8">
            <p>&copy; {{ new Date().getFullYear() }} KEDEBAH ERP. All rights reserved.</p>
          </div>
        </div>

        <!-- Right Side: Sign In Form -->
        <div class="flex-1 lg:w-3/5 p-8 sm:p-10 lg:p-12 xl:p-16 flex items-center">
          <div class="w-full max-w-md mx-auto">
            
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8 text-center">
              <img 
                src="/images/Kedebah Logo.png" 
                alt="KEDEBAH ERP" 
                class="h-10 w-auto mx-auto"
              />
            </div>

            <!-- Header -->
            <div class="mb-8 lg:mb-10">
              <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-gray-50 mb-2">
                Sign In
              </h2>
              <p class="text-gray-600 dark:text-gray-400">
                Enter your credentials to access your account
              </p>
            </div>

            <!-- Login Form -->
            <form class="space-y-6" @submit.prevent="submit">
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
              <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="form.remember"
                    type="checkbox"
                    class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500 focus:ring-offset-0 transition"
                  />
                  <span class="text-sm text-gray-700 dark:text-gray-300">Remember me</span>
                </label>

                <Link
                  href="/forgot-password"
                  class="text-sm font-medium text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 transition-colors"
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
            <div class="relative my-8">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                  New to our platform?
                </span>
              </div>
            </div>

            <!-- Register Link -->
            <div class="text-center">
              <Link
                href="/register"
                class="inline-flex items-center justify-center w-full px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
              >
                <i class="ri-user-add-line mr-2"></i>
                Create an Account
              </Link>
            </div>

            <!-- Additional Info -->
            <div class="text-center mt-6">
              <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                By signing in, you agree to our 
                <a href="#" class="text-green-600 hover:text-green-700 dark:text-green-400 hover:underline">Terms</a>
                and 
                <a href="#" class="text-green-600 hover:text-green-700 dark:text-green-400 hover:underline">Privacy Policy</a>
              </p>
            </div>
            
          </div>
        </div>
        
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Smooth transitions */
input[type="checkbox"] {
  cursor: pointer;
}

/* Animation for form elements */
form > * {
  animation: fadeInUp 0.5s ease-out;
  animation-fill-mode: both;
}

form > *:nth-child(1) { animation-delay: 0.1s; }
form > *:nth-child(2) { animation-delay: 0.2s; }
form > *:nth-child(3) { animation-delay: 0.3s; }
form > *:nth-child(4) { animation-delay: 0.4s; }

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
</style>