import React, { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom';
import axiosClient from '../axios_client';
import { useStateContext } from '../contexts/ContextProvider';

export default function UserForm() {
    const navigate = useNavigate();
    const { id } = useParams();
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState(null);
    const { notification, setNotification } = useStateContext();
    const [user, setUser] = useState({
        id: null,
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    if (id) {
        useEffect(() => {
            setLoading(true);
            axiosClient.get(`/users/${id}`)
                .then(({ data }) => {
                    setLoading(false);
                    setUser(data);
                })
                .catch(() => {
                    setLoading(false);
                })
        }, [])

    }
    const onSubmit = (e) => {
        e.preventDefault();
        if (user.id) {
            axiosClient.put(`/users/${user.id}`, user)
                .then(() => {
                    // TODO show notification
                    setNotification('User was successfully updaed');
                    navigate('/users');
                })
                .catch(err => {
                    const response = err.response;
                    if (response.status === 422) {
                        setErrors(response.data.errors)
                    }
                })
        } else {
            axiosClient.post(`/users`, user)
                .then(() => {
                    // TODO show notification
                    setNotification('User was successfully created');
                    navigate('/users');
                })
                .catch(err => {
                    const response = err.response;
                    if (response.status === 422) {
                        setErrors(response.data.errors)
                    }
                })
        }
    }
    return (
        <div>
            {user.id && <h1>Update User:{user.name}</h1>}
            {!user.id && <h1>New User</h1>}
            <div className='card animated fadeInDown'>
                {loading && (
                    <div className="text-center">Loading</div>
                )}
            </div>
            {errors && <div className='alert'>
            {Object.keys(errors).map(key => (
              <p key={key}>{errors[key][0]}</p>
            ))}
          </div>
          }
            {!loading &&
                <form onSubmit={onSubmit}>
                    <input type="text" value={user.name} onChange={(e) => setUser({ ...user, name: e.target.value })} placeholder='Name' />
                    <input type="email" value={user.email} onChange={(e) => setUser({ ...user, email: e.target.value })} placeholder='Email' />
                    <input type="password" onChange={(e) => setUser({ ...user, password: e.target.value })} placeholder='Password' />
                    <input type="password" onChange={(e) => setUser({ ...user, password_confirmation: e.target.value })} placeholder='Confirm Password' />
                    <button className='btn'>Submit</button>
                </form>
            }
        </div>
    )
}
