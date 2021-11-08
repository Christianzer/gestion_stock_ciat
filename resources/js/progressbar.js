import VueProgressBar from 'vue-progressbar';

Vue.use(VueProgressBar, {
    color: '#034a9b',
    failedColor: '#ec0c0c',
    thickness: '5px',
    transition: {
        speed: '0.10s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    location: 'left',
    inverse: false,
})
