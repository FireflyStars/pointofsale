
<template>

    <div 
        :id="id"
        class="popup"
        :class="{ 'popup__show': isOpenModal(id) || open }"
    >
        <div 
            class="popup-inner" 
            :class="['popup-inner' + sizeClasses, classes]"
            :style="$attrs.style"
        >
            <div 
                class="popup-content p-5" 
                :class="$attrs.contentClasses || ''"
            >
                <slot></slot>
            </div>
            
            <a 
                class="popup__close" 
                href="#" 
                @click.prevent="toggleModal(id, false)"
                v-if="!readonly"
            >
                &times;
            </a>

        </div>
    </div>

</template>

<script>

import { computed } from 'vue'
import useModal from '../../composables/useModal'
import useKeydown from '../../composables/useKeydown'

export default {
    
    inheritAttrs: false,

    props: {
        open: {
            required: false,
            type: Boolean,
            default: false
        },
        backdrop: {
            type: Boolean,
            default: true
        },
        id: {
            required: true,
            type: String
        },
        size: {
            required: false,
            type: String,
            default: 'sm',
            validator(value) {
                return ['sm', 'md', 'lg'].includes(value)
            }
        },
        classes: {
            required: false,
            type: String,
            default: ''
        },

        readonly: {
            required: false,
            type: Boolean,
            default: false
        },
    },

    setup(props) {

        const { toggleModal, isOpenModal, modalConfig } = useModal()

        if(props.backdrop) {
            useKeydown([{ key: 'Escape', fn: () => toggleModal(props.id, false) }])
        }

        const sizeClasses = computed(() => {
            switch(props.size) {
                case 'lg': return '__lg'
                case 'sm': return '__sm'
                default: return ''
            }
        })
        
        return {
            toggleModal,
            isOpenModal,
            sizeClasses,
            modalConfig
        }

    }

}
</script>

<style scoped lang="scss">

@import url('https://fonts.googleapis.com/css?family=Raleway:300,400,600&subset=latin-ext');

$main-color: #9191E9;

.button {
  text-decoration: none;
  font-size: .875rem;
  font-weight: 300;
  text-transform: uppercase;
  display: inline-block;
  border-radius: 1.5rem;
  background-color: #fff;
  color: $main-color;
  padding: 1rem 2rem;
}

.popup {
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  width: 100vw;
  height: 100vh;
  bottom: 0;
  right: 0;
  background-color: rgba(0, 0, 0, .80);
  z-index: 99999999999999 !important;
  visibility: hidden;
  opacity: 0;
  overflow: hidden;
  transition: .4s ease-in-out;
  &-inner {
    position: relative;
    bottom: 0;
    right: 0;
    max-width: 700px;
    max-height: 530px;
    width: 100%;
    height: 100%;
    background-color: #fff;
    transition: .4s ease-in-out;
    &__lg {
        max-width: 1000px;
        max-height: 600px;
    }
    &__sm {
        max-width: 450px;
        max-height: 250px;
    }
  }

  &-content {

    height: 100%;

    overflow-y: auto;
    overflow-x: hidden;

    h1 {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 2rem;
      text-transform: uppercase;
      color: #0A0A0A;
    }
    p {
      font-size: .875rem;
      color: #686868;
      line-height: 1.5;
    }
  }
  &__show {
    visibility: visible;
    opacity: 1;
  }
  &__close {
    position: absolute;
    right: -.5rem;
    top: -.5rem;
    width: 1.5rem;
    height: 1.5rem;
    font-size: .875rem;
    font-weight: 300;
    border-radius: 100%;
    background-color: #0A0A0A;
    z-index: 4;
    color: #fff;
    line-height: 1.5rem;
    text-align: center;
    cursor: pointer;
    text-decoration: none;
  }
}

.popup-content::-webkit-scrollbar {
    width: 5px;
}
 
.popup-content::-webkit-scrollbar-track {
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
}
 
.popup-content::-webkit-scrollbar-thumb {
  background-color: darkgrey;
  outline: 1px solid rgb(76, 87, 97);
}

</style>