<template>
    <!-- Search -->
    <div class="mb-2">
        <form action="#">
            <div class="input-group">
                <div class="input-group-text">
                    <div class="icon icon-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                </div>

                <input type="text" class="form-control form-control-lg ps-0" placeholder="Search messages or users" aria-label="Search for messages or users...">
            </div>
        </form>
    </div>
</template>
<script>
export default {
    
}
</script>
<style scoped>
.input-group-text {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 0.59375rem 1.125rem;
    font-size: .9375rem;
    font-weight: 400;
    line-height: 1.6;
    color: #a0b3ce;
    text-align: center;
    white-space: nowrap;
    background-color: #ebf1f7;
    border: 2px solid #ebf1f7;
    border-radius: 0.6rem;
}
.form-control{
    font-weight: 400;
    line-height: 1.6;
    color: #a0b3ce;
    background-color: #ebf1f7;
    background-clip: padding-box;    
    border: 2px solid #ebf1f7;
    appearance: none;
    min-height: calc(1.6em + 1.75rem + 4px);
    padding: 0.875rem 1.125rem;
    font-size: .9375rem;
    border-radius: 0.6rem;    
}
.form-control:focus,
.form-control:active{
    outline: none;
    box-shadow: none;
}
</style>