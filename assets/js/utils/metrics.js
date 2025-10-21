// REST metrics helper.
//
// Provides a lightweight wrapper for submitting CTA metrics to the
// pf2/v1/metrics endpoint with nonce headers.

const resolveRestConfig = () => {
  if (typeof window === 'undefined') {
    return { restUrl: '', nonce: '', postId: 0 };
  }

  const config = window.pf2Rest || {};

  return {
    restUrl: typeof config.restUrl === 'string' ? config.restUrl : '',
    nonce: typeof config.nonce === 'string' ? config.nonce : '',
    postId: Number.isInteger(config.postId) ? config.postId : 0,
  };
};

const buildRequestUrl = (restUrl) => {
  if (!restUrl) {
    return '/wp-json/pf2/v1/metrics';
  }

  const trimmed = restUrl.replace(/\/$/, '');
  return `${trimmed}/pf2/v1/metrics`;
};

/**
 * Submit a metric payload to the REST API.
 *
 * @param {string} type Metric type identifier.
 * @param {object} payload Additional payload values.
 * @returns {Promise<object>}
 */
export async function pf2SendMetric(type, payload = {}) {
  const config = resolveRestConfig();
  const url = buildRequestUrl(config.restUrl);
  const headers = {
    'Content-Type': 'application/json',
  };

  if (config.nonce) {
    headers['X-WP-Nonce'] = config.nonce;
  }

  const body = {
    type,
    ...payload,
  };

  if (typeof body.pid !== 'number') {
    body.pid = config.postId || 0;
  }

  try {
    const response = await fetch(url, {
      method: 'POST',
      headers,
      body: JSON.stringify(body),
      credentials: 'same-origin',
    });

    if (!response.ok) {
      return { ok: false };
    }

    return await response.json();
  } catch (error) {
    return { ok: false };
  }
}
