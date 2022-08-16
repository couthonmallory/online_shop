import { ref } from "vue";


export default function useStripe(){

    const elements = ref(null);

    const initalize = async() =>{

        const stripe = Stripe(process.env.MIX_STRIPE_TEST_PUBLIC_KEY);

        const clientSecret = await axios.post('/paymentIntent')
           .then(r => r.data.clientSecret)
           .catch(err => console.log(err))
     
          elements.value = stripe.elements({ clientSecret });

        
          const paymentElement = elements.value.create("payment");
          paymentElement.mount("#payment-element");  
        }

        return {
            initialize
        }
    }
